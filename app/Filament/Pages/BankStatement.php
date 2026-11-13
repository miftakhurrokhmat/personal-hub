<?php

namespace App\Filament\Pages;

use App\Models\BankAccount;
use App\Models\BankAccountBalance;
use App\Models\Expense;
use App\Models\Income;
use Carbon\Carbon;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BankStatement extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Mutasi Rekening';
    protected static ?string $title = 'Mutasi Rekening & Saldo Akhir';
    protected static ?string $navigationGroup = 'Finansial & Arus Kas'; // Sesuaikan dengan grup navigasi Anda
    protected static ?int $navigationSort = 4;

    protected static string $view = 'filament.pages.bank-statement';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'bank_account_id' => null,
            'year_month' => now()->format('Y-m'),
        ]);
    }

    public static function shouldRegisterNavigation(): bool
    {
        $role = session('active_role', 'all');
        return in_array($role, ['all', 'manajer']);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Filter Mutasi Rekening')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('bank_account_id')
                                    ->label('Rekening / Bank')
                                    ->placeholder('Pilih Rekening Terlebih Dahulu...')
                                    ->options(BankAccount::where('is_active', true)->pluck('name', 'id'))
                                    ->searchable()
                                    ->live(),

                                Select::make('year_month')
                                    ->label('Periode (Tahun - Bulan)')
                                    ->placeholder('Pilih Periode...')
                                    ->options($this->getAvailablePeriods())
                                    ->live(),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getAvailablePeriods(): array
    {
        $incomeMonths = Income::query()
            ->selectRaw("DATE_FORMAT(COALESCE(received_at, due_date, created_at), '%Y-%m') as ym")
            ->distinct()
            ->pluck('ym');

        $expenseMonths = Expense::query()
            ->whereNotNull('spent_at')
            ->selectRaw("DATE_FORMAT(spent_at, '%Y-%m') as ym")
            ->distinct()
            ->pluck('ym');

        $balanceMonths = BankAccountBalance::query()
            ->selectRaw("DATE_FORMAT(recorded_date, '%Y-%m') as ym")
            ->distinct()
            ->pluck('ym');

        return $incomeMonths
            ->merge($expenseMonths)
            ->merge($balanceMonths)
            ->push(now()->format('Y-m'))
            ->filter()
            ->unique()
            ->sortDesc()
            ->mapWithKeys(function ($ym) {
                $label = Carbon::createFromFormat('Y-m', $ym)->translatedFormat('Y - F');
                return [$ym => $label];
            })
            ->toArray();
    }

    public function getStatementData(): ?array
    {
        $bankAccountId = $this->data['bank_account_id'] ?? null;
        $yearMonth = $this->data['year_month'] ?? null;

        if (! $bankAccountId || ! $yearMonth) {
            return null;
        }

        $account = BankAccount::find($bankAccountId);
        if (! $account) {
            return null;
        }

        $periodStart = Carbon::createFromFormat('Y-m', $yearMonth)->startOfMonth();
        $periodEnd = Carbon::createFromFormat('Y-m', $yearMonth)->endOfMonth();

        // 1. Ambil catatan saldo awal terakhir pada atau sebelum akhir periode terpilih
        $anchorBalance = BankAccountBalance::where('bank_account_id', $bankAccountId)
            ->whereDate('recorded_date', '<=', $periodEnd->toDateString())
            ->orderByDesc('recorded_date')
            ->orderByDesc('id')
            ->first();

        $openingBalance = 0;
        $openingDate = $periodStart->copy();
        $openingNote = 'Saldo Awal Periode';
        $effectiveStartDate = $periodStart->copy();

        if ($anchorBalance) {
            $anchorDate = Carbon::parse($anchorBalance->recorded_date)->startOfDay();
            $openingBalance = (float) $anchorBalance->balance;

            if ($anchorDate->lt($periodStart)) {
                // Hitung mutasi antara tanggal pencatatan saldo hingga sebelum awal bulan terpilih
                $priorIncomes = (float) Income::where('bank_account_id', $bankAccountId)
                    ->where('payment_status', 'paid')
                    ->whereDate(DB::raw('COALESCE(received_at, due_date, created_at)'), '>=', $anchorDate->toDateString())
                    ->whereDate(DB::raw('COALESCE(received_at, due_date, created_at)'), '<', $periodStart->toDateString())
                    ->sum('amount');

                $priorExpenses = (float) Expense::where('bank_account_id', $bankAccountId)
                    ->whereDate('spent_at', '>=', $anchorDate->toDateString())
                    ->whereDate('spent_at', '<', $periodStart->toDateString())
                    ->sum('amount');

                $openingBalance = $openingBalance + $priorIncomes - $priorExpenses;
                $openingDate = $periodStart->copy();
                $openingNote = 'Saldo Awal Bulan (Carry Forward)';
            } else {
                // Saldo awal dicatat di dalam bulan terpilih
                $openingDate = $anchorDate;
                $effectiveStartDate = $anchorDate;
                $openingNote = $anchorBalance->note ?: 'Saldo Awal Tercatat';
            }
        }

        // 2. Ambil semua pemasukan (kredit) pada periode efektif
        $incomes = Income::where('bank_account_id', $bankAccountId)
            ->where('payment_status', 'paid')
            ->whereDate(DB::raw('COALESCE(received_at, due_date, created_at)'), '>=', $effectiveStartDate->toDateString())
            ->whereDate(DB::raw('COALESCE(received_at, due_date, created_at)'), '<=', $periodEnd->toDateString())
            ->get()
            ->map(function ($item) {
                $txDate = $item->received_at ?? $item->due_date ?? $item->created_at;
                return [
                    'date' => Carbon::parse($txDate)->format('Y-m-d'),
                    'sort_key' => Carbon::parse($txDate)->format('Y-m-d') . '-1-' . str_pad($item->id, 8, '0', STR_PAD_LEFT),
                    'description' => $item->title,
                    'category' => $item->source ?? '-',
                    'credit' => (float) $item->amount,
                    'debit' => 0.0,
                ];
            });

        // 3. Ambil semua pengeluaran (debet) pada periode efektif
        $expenses = Expense::where('bank_account_id', $bankAccountId)
            ->whereDate('spent_at', '>=', $effectiveStartDate->toDateString())
            ->whereDate('spent_at', '<=', $periodEnd->toDateString())
            ->get()
            ->map(function ($item) {
                return [
                    'date' => Carbon::parse($item->spent_at)->format('Y-m-d'),
                    'sort_key' => Carbon::parse($item->spent_at)->format('Y-m-d') . '-2-' . str_pad($item->id, 8, '0', STR_PAD_LEFT),
                    'description' => $item->title,
                    'category' => $item->category ?? '-',
                    'credit' => 0.0,
                    'debit' => (float) $item->amount,
                ];
            });

        // 4. Gabungkan dan urutkan berdasarkan tanggal transaksi, lalu hitung running balance
        $transactions = collect()
            ->merge($incomes)
            ->merge($expenses)
            ->sortBy('sort_key')
            ->values();

        $runningBalance = $openingBalance;
        $totalCredit = 0.0;
        $totalDebit = 0.0;

        $rows = $transactions->map(function ($tx) use (&$runningBalance, &$totalCredit, &$totalDebit) {
            $runningBalance = $runningBalance + $tx['credit'] - $tx['debit'];
            $totalCredit += $tx['credit'];
            $totalDebit += $tx['debit'];
            $tx['balance'] = $runningBalance;
            return $tx;
        });

        return [
            'account' => $account,
            'period_label' => $periodStart->translatedFormat('F Y'),
            'opening_date' => $openingDate->format('d M Y'),
            'opening_note' => $openingNote,
            'opening_balance' => $openingBalance,
            'rows' => $rows,
            'total_credit' => $totalCredit,
            'total_debit' => $totalDebit,
            'closing_balance' => $runningBalance,
            'closing_date' => $periodEnd->isFuture() ? now()->format('d M Y') : $periodEnd->format('d M Y'),
        ];
    }
}
