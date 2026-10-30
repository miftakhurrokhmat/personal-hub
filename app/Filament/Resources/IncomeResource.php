<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IncomeResource\Pages;
use App\Filament\Resources\IncomeResource\RelationManagers;
use App\Models\Income;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class IncomeResource extends Resource
{
    protected static ?string $model = Income::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-up'; // atau 'heroicon-o-banknotes'
    protected static ?string $navigationGroup = 'Keuangan & Kas';
    protected static ?string $navigationLabel = 'Pemasukan & Tagihan';

    public static function shouldRegisterNavigation(): bool
    {
        $role = session('active_role', 'all');
        return in_array($role, ['all', 'manajer']);
    }

    public static function form(Form $form): Form
    {
        // return $form
        //     ->schema([
        //         Forms\Components\Select::make('project_id')
        //             ->relationship('project', 'title')
        //             ->default(null),
        //         Forms\Components\Select::make('client_id')
        //             ->relationship('client', 'name')
        //             ->default(null),
        //         Forms\Components\Select::make('bank_account_id')
        //             ->relationship('bankAccount', 'name')
        //             ->default(null),
        //         Forms\Components\TextInput::make('title')
        //             ->required()
        //             ->maxLength(255),
        //         Forms\Components\TextInput::make('source')
        //             ->required(),
        //         Forms\Components\TextInput::make('amount')
        //             ->required()
        //             ->numeric(),
        //         Forms\Components\TextInput::make('payment_status')
        //             ->required(),
        //         Forms\Components\DatePicker::make('billed_at'),
        //         Forms\Components\DatePicker::make('due_date'),
        //         Forms\Components\DatePicker::make('received_at'),
        //     ]);

        return $form
            ->schema([
                Forms\Components\Section::make('Detail Pemasukan & Penagihan')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->placeholder('misal: Honor Asesi LSP Batch 2 / Invoice Tahap 1 Dev')
                            ->label('Deskripsi / Judul'),
                        Forms\Components\Select::make('source')
                            ->options([
                                'kampus' => 'Gaji / Honor Kampus',
                                'asesor' => 'Asesor Sertifikasi / LSP',
                                'trainer' => 'Trainer / Workshop',
                                'proyek_it' => 'Proyek IT / Dev',
                                'hibah' => 'Hibah Penelitian / Pengabdian',
                                'lainnya' => 'Lainnya',
                            ])
                            ->required()
                            ->label('Sumber Pendapatan'),
                        Forms\Components\Select::make('project_id')
                            ->relationship('project', 'title')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->label('Terkait Proyek (Opsional)'),
                        Forms\Components\Select::make('client_id')
                            ->relationship('client', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->label('Klien / Pembayar (Opsional)'),
                        Forms\Components\TextInput::make('amount')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->label('Nominal (Rp)'),
                        Forms\Components\Select::make('payment_status')
                            ->options([
                                'draft' => 'Draft (Belum Diterbitkan)',
                                'billed' => 'Sudah Ditagihkan (Menunggu Pembayaran)',
                                'paid' => 'Lunas / Sudah Cair',
                            ])
                            ->default('paid')
                            ->required()
                            ->live()
                            ->label('Status Pembayaran'),
                        Forms\Components\Select::make('bank_account_id')
                            ->relationship('bankAccount', 'name')
                            ->nullable()
                            ->label('Masuk ke Rekening / Kas')
                            ->visible(fn(Forms\Get $get) => $get('payment_status') === 'paid'),
                        Forms\Components\DatePicker::make('billed_at')
                            ->label('Tanggal Tagihan Dikirim')
                            ->visible(fn(Forms\Get $get) => in_array($get('payment_status'), ['billed', 'paid'])),
                        Forms\Components\DatePicker::make('due_date')
                            ->label('Jatuh Tempo Pembayaran')
                            ->visible(fn(Forms\Get $get) => $get('payment_status') === 'billed'),
                        Forms\Components\DatePicker::make('received_at')
                            ->label('Tanggal Uang Diterima')
                            ->visible(fn(Forms\Get $get) => $get('payment_status') === 'paid'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        // return $table
        //     ->columns([
        //         Tables\Columns\TextColumn::make('project.title')
        //             ->numeric()
        //             ->sortable(),
        //         Tables\Columns\TextColumn::make('client.name')
        //             ->numeric()
        //             ->sortable(),
        //         Tables\Columns\TextColumn::make('bankAccount.name')
        //             ->numeric()
        //             ->sortable(),
        //         Tables\Columns\TextColumn::make('title')
        //             ->searchable(),
        //         Tables\Columns\TextColumn::make('source'),
        //         Tables\Columns\TextColumn::make('amount')
        //             ->numeric()
        //             ->sortable(),
        //         Tables\Columns\TextColumn::make('payment_status'),
        //         Tables\Columns\TextColumn::make('billed_at')
        //             ->date()
        //             ->sortable(),
        //         Tables\Columns\TextColumn::make('due_date')
        //             ->date()
        //             ->sortable(),
        //         Tables\Columns\TextColumn::make('received_at')
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
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Deskripsi')->searchable(),
                Tables\Columns\TextColumn::make('client.name')->label('Klien/Instansi')->default('-'),
                Tables\Columns\BadgeColumn::make('source')->label('Sumber'),
                Tables\Columns\TextColumn::make('amount')->money('IDR')->label('Nominal'),
                Tables\Columns\BadgeColumn::make('payment_status')
                    ->label('Status')
                    ->colors([
                        'gray' => 'draft',
                        'danger' => 'billed',
                        'success' => 'paid',
                    ]),
                Tables\Columns\TextColumn::make('due_date')->date()->label('Jatuh Tempo')->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('belum_lunas')
                    ->label('Tampilkan Tagihan Belum Dibayar')
                    ->query(fn(Builder $query): Builder => $query->where('payment_status', 'billed')),
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
            'index' => Pages\ListIncomes::route('/'),
            'create' => Pages\CreateIncome::route('/create'),
            'edit' => Pages\EditIncome::route('/{record}/edit'),
        ];
    }
}
