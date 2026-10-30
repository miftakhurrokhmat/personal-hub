<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Debt;
use App\Models\MonthlyTarget;
use Carbon\Carbon;

class FinancialHealthOverview extends BaseWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $currentPeriod = Carbon::now()->format('Y-m');

        $target = MonthlyTarget::where('period', $currentPeriod)->first();
        $targetIncome = $target ? $target->income_target : 15000000;
        $limitExpense = $target ? $target->max_expense_limit : 8000000;

        // Pemasukan lunas bulan ini
        $realizedIncome = Income::whereYear('received_at', $currentYear)
            ->whereMonth('received_at', $currentMonth)
            ->where('payment_status', 'paid')
            ->sum('amount');

        // Pengeluaran bulan ini
        $realizedExpense = Expense::whereYear('spent_at', $currentYear)
            ->whereMonth('spent_at', $currentMonth)
            ->sum('amount');

        // Tagihan invoice yang ditagih + piutang perorangan yang belum lunas
        $unpaidIncomes = Income::where('payment_status', 'billed')->sum('amount');
        $receivables = Debt::where('type', 'piutang_orang')->where('status', '!=', 'settled')->sum('total_amount');
        $totalTagihanTertahan = $unpaidIncomes + $receivables;

        $incomeProgress = round(($realizedIncome / max($targetIncome, 1)) * 100);
        $expenseProgress = round(($realizedExpense / max($limitExpense, 1)) * 100);

        $isOverExpense = $realizedExpense >= $limitExpense;
        $isWarningExpense = $expenseProgress >= 80;
        $expenseColor = $isOverExpense ? 'danger' : ($isWarningExpense ? 'warning' : 'success');
        $expenseDesc = $isOverExpense 
            ? 'REM AKTIF: Menembus batas!' 
            : ($isWarningExpense ? "Perhatian: {$expenseProgress}% terpakai" : "Aman ({$expenseProgress}%)");

        return [
            Stat::make('Pemasukan Bulan Ini', 'Rp ' . number_format($realizedIncome, 0, ',', '.'))
                ->description("Target: Rp " . number_format($targetIncome, 0, ',', '.') . " ({$incomeProgress}%)")
                ->descriptionIcon($incomeProgress >= 100 ? 'heroicon-m-check-badge' : 'heroicon-m-arrow-trending-up')
                ->color($incomeProgress >= 100 ? 'success' : 'primary'),

            Stat::make('Rem Pengeluaran', 'Rp ' . number_format($realizedExpense, 0, ',', '.'))
                ->description($expenseDesc)
                ->descriptionIcon($isWarningExpense ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-shield-check')
                ->color($expenseColor),

            Stat::make('Tagihan Belum Cair', 'Rp ' . number_format($totalTagihanTertahan, 0, ',', '.'))
                ->description($totalTagihanTertahan > 0 ? 'Menunggu pembayaran' : 'Nihil')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($totalTagihanTertahan > 0 ? 'warning' : 'gray'),
        ];
    }
}