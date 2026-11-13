<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages;
// use App\Filament\Resources\ExpenseResource\RelationManagers;
use App\Models\Expense;
use App\Models\ExpenseReceipt;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
// use Illuminate\Database\Eloquent\SoftDeletingScope;
use Carbon\Carbon;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-down';
    protected static ?string $navigationGroup = 'Keuangan & Kas';
    protected static ?string $navigationLabel = 'Pengeluaran';

    public static function shouldRegisterNavigation(): bool
    {
        $role = session('active_role', 'all');
        return in_array($role, ['all', 'manajer']);
    }

    public static function form(Form $form): Form
    {
        // return $form
        //     ->schema([
        //         Forms\Components\Select::make('bank_account_id')
        //             ->relationship('bankAccount', 'name')
        //             ->default(null),
        //         Forms\Components\Select::make('project_id')
        //             ->relationship('project', 'title')
        //             ->default(null),
        //         Forms\Components\TextInput::make('title')
        //             ->required()
        //             ->maxLength(255),
        //         Forms\Components\TextInput::make('category')
        //             ->required(),
        //         Forms\Components\TextInput::make('amount')
        //             ->required()
        //             ->numeric(),
        //         Forms\Components\DatePicker::make('spent_at')
        //             ->required(),
        //     ]);

        return $form
            ->schema([
                Forms\Components\Section::make('Catatan Pengeluaran')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->placeholder('misal: Sewa VPS Cloud / Bensin Dosen / Belanja Dapur')
                            ->label('Keperluan / Judul'),
                        Forms\Components\Select::make('category')
                            ->options([
                                'operasional_it' => 'Operasional IT & Server',
                                'rumah_tangga' => 'Keluarga & Rumah Tangga',
                                'peralatan' => 'Peralatan / Gadget',
                                'sosial_warga' => 'Sosial Warga & Desa',
                                'transfer' => 'Transfer / Pindah Rekening',
                                'lainnya' => 'Lainnya',
                            ])
                            ->required()
                            ->label('Kategori Pengeluaran'),
                        Forms\Components\TextInput::make('amount')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->label('Nominal (Rp)'),
                        Forms\Components\Select::make('bank_account_id')
                            ->relationship('bankAccount', 'name')
                            ->nullable()
                            ->label('Dipotong dari Rekening / Kas'),
                        Forms\Components\Select::make('project_id')
                            ->relationship('project', 'title')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->label('Terkait Proyek (Opsional)'),
                        Forms\Components\DatePicker::make('spent_at')
                            ->default(now())
                            ->required()
                            ->label('Tanggal Pengeluaran'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        // return $table
        //     ->columns([
        //         Tables\Columns\TextColumn::make('bankAccount.name')
        //             ->numeric()
        //             ->sortable(),
        //         Tables\Columns\TextColumn::make('project.title')
        //             ->numeric()
        //             ->sortable(),
        //         Tables\Columns\TextColumn::make('title')
        //             ->searchable(),
        //         Tables\Columns\TextColumn::make('category'),
        //         Tables\Columns\TextColumn::make('amount')
        //             ->numeric()
        //             ->sortable(),
        //         Tables\Columns\TextColumn::make('spent_at')
        //             ->date()
        //             ->sortable(),
        //         Tables\Columns\TextColumn::make('created_at')
        //             ->dateTime()
        //             ->sortable()
        //             ->toggleable(isToggledHiddenByDefault: true),
        //         Tables\Columns\TextColumn::make('updated_at')
        //             ->dateTime()
        //             ->sortable()
        //             ->toggleable(isToggledHiddenByDefault: true),
        //     ])
        //     ->filters([
        //         //
        //     ])
        //     ->actions([
        //         Tables\Actions\EditAction::make(),
        //     ])
        //     ->bulkActions([
        //         Tables\Actions\BulkActionGroup::make([
        //             Tables\Actions\DeleteBulkAction::make(),
        //         ]),
        //     ]);

        // return $table
        //     ->defaultSort('spent_at', 'desc')
        //     ->columns([
        //         Tables\Columns\TextColumn::make('spent_at')->date()->label('Tanggal')->sortable(),
        //         Tables\Columns\TextColumn::make('title')->label('Keperluan')->searchable(),
        //         Tables\Columns\BadgeColumn::make('category')->label('Kategori'),
        //         Tables\Columns\TextColumn::make('amount')->money('IDR')->label('Nominal')->sortable(),
        //         Tables\Columns\TextColumn::make('bankAccount.name')->label('Sumber Dana')->default('-'),
        //         Tables\Columns\TextColumn::make('project.title')->label('Proyek')->default('-'),
        //     ]);

        return $table
            ->defaultSort('spent_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('spent_at')
                    ->date()
                    ->label('Tanggal')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Keperluan')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('category')
                    ->label('Kategori'),
                Tables\Columns\TextColumn::make('amount')
                    ->money('IDR')
                    ->label('Nominal')
                    ->sortable(),
                Tables\Columns\TextColumn::make('bankAccount.name')
                    ->label('Sumber Dana')
                    ->default('-'),
                Tables\Columns\TextColumn::make('project.title')
                    ->label('Proyek')
                    ->default('-'),
            ])
            ->filters([
                // 1. Filter Tahun - Bulan berdasarkan kolom 'spent_at'
                Tables\Filters\SelectFilter::make('year_month')
                    ->label('Tahun - Bulan')
                    ->placeholder('Semua Periode')
                    ->options(function () {
                        return Expense::query()
                            ->whereNotNull('spent_at')
                            ->selectRaw("DATE_FORMAT(spent_at, '%Y-%m') as ym")
                            ->distinct()
                            ->orderByDesc('ym')
                            ->pluck('ym')
                            ->mapWithKeys(function ($ym) {
                                $label = Carbon::createFromFormat('Y-m', $ym)->translatedFormat('Y - F');
                                return [$ym => $label]; // Contoh: "2026 - November"
                            })
                            ->toArray();
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['value'])) {
                            return $query;
                        }

                        [$year, $month] = explode('-', $data['value']);

                        return $query
                            ->whereYear('spent_at', $year)
                            ->whereMonth('spent_at', $month);
                    }),

                // 2. Filter Sumber Dana berdasarkan relasi 'bankAccount'
                Tables\Filters\SelectFilter::make('bank_account_id')
                    ->label('Sumber Dana')
                    ->placeholder('Semua Sumber Dana')
                    ->relationship('bankAccount', 'name')
                    ->searchable()
                    ->preload(),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(2)
            ->actions([
                Tables\Actions\EditAction::make(),

                // Aksi Terbitkan Kwitansi Baru
                Tables\Actions\Action::make('createReceipt')
                    ->label('Terbitkan Kwitansi')
                    ->icon('heroicon-o-receipt-percent')
                    ->color('warning')
                    ->visible(fn($record) => ! ExpenseReceipt::where('expense_id', $record->id)->exists())
                    ->form([
                        Forms\Components\TextInput::make('receipt_number')
                            ->label('Nomor Kwitansi')
                            ->required()
                            ->default(fn() => 'KW/' . date('Y/m') . '/' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT)),

                        Forms\Components\DatePicker::make('receipt_date')
                            ->label('Tanggal Kwitansi')
                            ->required()
                            ->default(now()),

                        Forms\Components\TextInput::make('recipient_name')
                            ->label('Telah Terima Dari')
                            ->default('Miftakhur Rokhmat')
                            ->required(),

                        Forms\Components\TextInput::make('payer_name')
                            ->label('Diserahkan Kepada / Penerima Dana')
                            ->required()
                            ->placeholder('misal: Nama Toko / Vendor / Penerima'),

                        Forms\Components\TextInput::make('amount')
                            ->label('Jumlah Uang (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(fn($record) => $record->amount)
                            ->required(),

                        Forms\Components\Textarea::make('for_payment')
                            ->label('Guna Pembayaran')
                            ->default(fn($record) => $record->title)
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        $receipt = ExpenseReceipt::create([
                            'expense_id' => $record->id,
                            'receipt_number' => $data['receipt_number'],
                            'receipt_date' => $data['receipt_date'],
                            'recipient_name' => $data['recipient_name'],
                            'payer_name' => $data['payer_name'],
                            'amount' => $data['amount'],
                            'for_payment' => $data['for_payment'],
                        ]);

                        Notification::make()
                            ->title('Kwitansi Diterbitkan')
                            ->body("Kwitansi nomor {$receipt->receipt_number} berhasil dibuat.")
                            ->success()
                            ->send();
                    }),

                // Aksi Cetak PDF jika kwitansi sudah ada
                Tables\Actions\Action::make('printReceipt')
                    ->label('Cetak Kwitansi')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->visible(fn($record) => ExpenseReceipt::where('expense_id', $record->id)->exists())
                    ->url(function ($record) {
                        $receipt = ExpenseReceipt::where('expense_id', $record->id)->first();
                        return $receipt ? route('print.expense-receipt', $receipt) : '#';
                    })
                    ->openUrlInNewTab(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }
}
