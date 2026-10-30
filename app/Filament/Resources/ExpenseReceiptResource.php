<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseReceiptResource\Pages;
use App\Filament\Resources\ExpenseReceiptResource\RelationManagers;
use App\Models\ExpenseReceipt;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Expense;
use Filament\Notifications\Notification;

class ExpenseReceiptResource extends Resource
{
    protected static ?string $model = ExpenseReceipt::class;

    protected static ?string $navigationGroup = 'Keuangan & Kas';
    protected static ?string $navigationLabel = 'Kwitansi Pengeluaran';
    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent'; // atau 'heroicon-o-ticket'

    public static function shouldRegisterNavigation(): bool
    {
        $role = session('active_role', 'all');
        return in_array($role, ['all', 'manajer']);
    }

    public static function form(Form $form): Form
    {
        // return $form
        //     ->schema([
        //         Forms\Components\TextInput::make('expense_id')
        //             ->numeric()
        //             ->default(null),
        //         Forms\Components\TextInput::make('receipt_number')
        //             ->required()
        //             ->maxLength(255),
        //         Forms\Components\TextInput::make('recipient_name')
        //             ->required()
        //             ->maxLength(255),
        //         Forms\Components\TextInput::make('payer_name')
        //             ->required()
        //             ->maxLength(255),
        //         Forms\Components\TextInput::make('amount')
        //             ->required()
        //             ->numeric(),
        //         Forms\Components\Textarea::make('for_payment')
        //             ->required()
        //             ->columnSpanFull(),
        //         Forms\Components\DatePicker::make('receipt_date')
        //             ->required(),
        //     ]);

        return $form
            ->schema([
                Forms\Components\Section::make('Rincian Kwitansi Pengeluaran')
                    ->schema([
                        Forms\Components\TextInput::make('receipt_number')
                            ->label('Nomor Kwitansi')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->default(fn() => 'KW/' . date('Y/m') . '/' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT)),

                        Forms\Components\DatePicker::make('receipt_date')
                            ->label('Tanggal Transaksi')
                            ->required()
                            ->default(now()),

                        // Relasi ke Master Pengeluaran dengan autofill otomatis
                        Forms\Components\Select::make('expense_id')
                            ->label('Hubungkan ke Data Pengeluaran (Expense)')
                            ->relationship('expense', 'title')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($expense = Expense::find($state)) {
                                    $set('amount', $expense->amount);
                                    $set('for_payment', $expense->title);
                                }
                            })
                            ->helperText('Pilih pengeluaran terkait untuk mengisi otomatis nominal dan keterangannya.'),

                        Forms\Components\TextInput::make('amount')
                            ->label('Jumlah Uang (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),

                        Forms\Components\TextInput::make('recipient_name')
                            ->label('Telah Terima Dari')
                            ->default('Miftakhur Rokhmat')
                            ->required(),

                        Forms\Components\TextInput::make('payer_name')
                            ->label('Untuk Diserahkan Kepada / Penerima Dana')
                            ->required()
                            ->placeholder('misal: Toko Komputer Mega / Nama Vendor'),

                        Forms\Components\Textarea::make('for_payment')
                            ->label('Guna Pembayaran')
                            ->required()
                            ->columnSpanFull()
                            ->placeholder('misal: Pembelian komponen sensor IoT & mikrofon condenser'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        // return $table
        //     ->columns([
        //         Tables\Columns\TextColumn::make('expense_id')
        //             ->numeric()
        //             ->sortable(),
        //         Tables\Columns\TextColumn::make('receipt_number')
        //             ->searchable(),
        //         Tables\Columns\TextColumn::make('recipient_name')
        //             ->searchable(),
        //         Tables\Columns\TextColumn::make('payer_name')
        //             ->searchable(),
        //         Tables\Columns\TextColumn::make('amount')
        //             ->numeric()
        //             ->sortable(),
        //         Tables\Columns\TextColumn::make('receipt_date')
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

        return $table
            ->defaultSort('receipt_date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('receipt_number')
                    ->label('No. Kwitansi')
                    ->weight('bold')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('receipt_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('payer_name')
                    ->label('Penerima Dana')
                    ->searchable(),

                Tables\Columns\TextColumn::make('for_payment')
                    ->label('Keperluan')
                    ->limit(35),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Nominal')
                    ->money('IDR')
                    ->weight('bold')
                    ->sortable(),

                Tables\Columns\TextColumn::make('expense.title')
                    ->label('Ref Pengeluaran')
                    ->default('-')
                    ->badge()
                    ->color('gray'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Tables\Actions\Action::make('print')
                //     ->label('Cetak PDF')
                //     ->icon('heroicon-o-printer')
                //     ->color('success')
                //     ->url(fn($record) => route('print.expense-receipt', $record))
                //     ->openUrlInNewTab(),

                // Aksi 1: Terbitkan Kwitansi Baru
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

                // Aksi 2: Cetak PDF jika kwitansi sudah ada
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
            'index' => Pages\ListExpenseReceipts::route('/'),
            'create' => Pages\CreateExpenseReceipt::route('/create'),
            'edit' => Pages\EditExpenseReceipt::route('/{record}/edit'),
        ];
    }
}
