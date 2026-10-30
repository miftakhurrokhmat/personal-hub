<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BankAccountResource\Pages;
use App\Filament\Resources\BankAccountResource\RelationManagers;
use App\Models\BankAccount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BankAccountResource extends Resource
{
    protected static ?string $model = BankAccount::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Keuangan & Kas';
    protected static ?string $navigationLabel = 'Rekening & Wallet';

    public static function shouldRegisterNavigation(): bool
    {
        $role = session('active_role', 'all');
        return in_array($role, ['all', 'manajer']);
    }

    public static function form(Form $form): Form
    {
        // return $form
        //     ->schema([
        //         Forms\Components\TextInput::make('name')
        //             ->required()
        //             ->maxLength(255),
        //         Forms\Components\TextInput::make('type')
        //             ->required(),
        //         Forms\Components\TextInput::make('account_number')
        //             ->maxLength(255)
        //             ->default(null),
        //         Forms\Components\TextInput::make('phone_number')
        //             ->tel()
        //             ->maxLength(255)
        //             ->default(null),
        //         Forms\Components\TextInput::make('holder_name')
        //             ->maxLength(255)
        //             ->default(null),
        //         Forms\Components\TextInput::make('balance')
        //             ->required()
        //             ->numeric()
        //             ->default(0.00),
        //         Forms\Components\Toggle::make('is_active')
        //             ->required(),
        //     ]);

        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Rekening / Dompet')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->placeholder('misal: Bank Mandiri / Bank Jago / GoPay / Seabank')
                            ->label('Nama Akun / Bank'),
                        Forms\Components\Select::make('type')
                            ->options([
                                'bank' => 'Bank Konvensional',
                                'digital_bank' => 'Bank Digital (Jago, Seabank, dll)',
                                'ewallet' => 'E-Wallet (GoPay, OVO, Dana)',
                                'cash' => 'Kas Tunai / Cash',
                            ])
                            ->default('bank')
                            ->required()
                            ->live()
                            ->label('Tipe Akun'),
                        Forms\Components\TextInput::make('account_number')
                            ->nullable()
                            ->label('Nomor Rekening')
                            ->helperText('Kosongkan jika e-wallet atau kas tunai'),
                        Forms\Components\TextInput::make('phone_number')
                            ->tel()
                            ->nullable()
                            ->label('Nomor HP Terdaftar')
                            ->helperText('Diisi untuk Bank Jago / E-Wallet'),
                        Forms\Components\TextInput::make('holder_name')
                            ->nullable()
                            ->label('Atas Nama Pemilik'),
                        Forms\Components\TextInput::make('balance')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->required()
                            ->label('Saldo Awal / Berjalan'),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true)
                            ->label('Akun Masih Aktif Digunakan'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        // return $table
        //     ->columns([
        //         Tables\Columns\TextColumn::make('name')
        //             ->searchable(),
        //         Tables\Columns\TextColumn::make('type'),
        //         Tables\Columns\TextColumn::make('account_number')
        //             ->searchable(),
        //         Tables\Columns\TextColumn::make('phone_number')
        //             ->searchable(),
        //         Tables\Columns\TextColumn::make('holder_name')
        //             ->searchable(),
        //         Tables\Columns\TextColumn::make('balance')
        //             ->numeric()
        //             ->sortable(),
        //         Tables\Columns\IconColumn::make('is_active')
        //             ->boolean(),
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
                Tables\Columns\TextColumn::make('name')->label('Nama Rekening')->searchable(),
                Tables\Columns\BadgeColumn::make('type')->label('Tipe'),
                Tables\Columns\TextColumn::make('account_number')->label('No. Rekening')->default('-'),
                Tables\Columns\TextColumn::make('phone_number')->label('No. HP')->default('-'),
                Tables\Columns\TextColumn::make('balance')->money('IDR')->label('Saldo'),
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
            'index' => Pages\ListBankAccounts::route('/'),
            'create' => Pages\CreateBankAccount::route('/create'),
            'edit' => Pages\EditBankAccount::route('/{record}/edit'),
        ];
    }
}
