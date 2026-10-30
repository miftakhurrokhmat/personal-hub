<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DebtResource\Pages;
use App\Filament\Resources\DebtResource\RelationManagers;
use App\Models\Debt;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DebtResource extends Resource
{
    protected static ?string $model = Debt::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static ?string $navigationGroup = 'Keuangan & Kas';
    protected static ?string $navigationLabel = 'Utang & Piutang';

    public static function shouldRegisterNavigation(): bool
    {
        $role = session('active_role', 'all');
        return in_array($role, ['all', 'manajer']);
    }

    public static function form(Form $form): Form
    {
        // return $form
        //     ->schema([
        //         Forms\Components\Select::make('client_id')
        //             ->relationship('client', 'name')
        //             ->default(null),
        //         Forms\Components\TextInput::make('person_name')
        //             ->required()
        //             ->maxLength(255),
        //         Forms\Components\TextInput::make('type')
        //             ->required(),
        //         Forms\Components\TextInput::make('total_amount')
        //             ->required()
        //             ->numeric(),
        //         Forms\Components\TextInput::make('settled_amount')
        //             ->required()
        //             ->numeric()
        //             ->default(0.00),
        //         Forms\Components\DatePicker::make('due_date'),
        //         Forms\Components\TextInput::make('status')
        //             ->required(),
        //         Forms\Components\Textarea::make('notes')
        //             ->columnSpanFull(),
        //     ]);

        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Utang / Piutang')
                    ->schema([
                        Forms\Components\TextInput::make('person_name')
                            ->required()
                            ->placeholder('Nama orang atau pihak terkait')
                            ->label('Nama Pihak Terkait'),
                        Forms\Components\Select::make('type')
                            ->options([
                                'utang_saya' => 'Utang Saya (Kewajiban Bayar)',
                                'piutang_orang' => 'Piutang Orang Lain (Hak Tagih Saya)',
                            ])
                            ->required()
                            ->label('Jenis'),
                        Forms\Components\Select::make('client_id')
                            ->relationship('client', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->label('Terkait Klien/Mitra (Opsional)'),
                        Forms\Components\TextInput::make('total_amount')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->label('Total Nominal'),
                        Forms\Components\TextInput::make('settled_amount')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->required()
                            ->label('Nominal yang Sudah Terbayar'),
                        Forms\Components\DatePicker::make('due_date')
                            ->label('Target Jatuh Tempo'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'unpaid' => 'Belum Lunas',
                                'partially_paid' => 'Sebagian / Cicil',
                                'settled' => 'Lunas',
                            ])
                            ->default('unpaid')
                            ->required()
                            ->label('Status Pelunasan'),
                        Forms\Components\Textarea::make('notes')
                            ->nullable()
                            ->columnSpanFull()
                            ->label('Catatan'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        // return $table
        //     ->columns([
        //         Tables\Columns\TextColumn::make('client.name')
        //             ->numeric()
        //             ->sortable(),
        //         Tables\Columns\TextColumn::make('person_name')
        //             ->searchable(),
        //         Tables\Columns\TextColumn::make('type'),
        //         Tables\Columns\TextColumn::make('total_amount')
        //             ->numeric()
        //             ->sortable(),
        //         Tables\Columns\TextColumn::make('settled_amount')
        //             ->numeric()
        //             ->sortable(),
        //         Tables\Columns\TextColumn::make('due_date')
        //             ->date()
        //             ->sortable(),
        //         Tables\Columns\TextColumn::make('status'),
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
                Tables\Columns\TextColumn::make('person_name')->label('Nama Pihak')->searchable(),
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Jenis')
                    ->colors([
                        'danger' => 'utang_saya',
                        'success' => 'piutang_orang',
                    ]),
                Tables\Columns\TextColumn::make('total_amount')->money('IDR')->label('Total'),
                Tables\Columns\TextColumn::make('settled_amount')->money('IDR')->label('Terbayar'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'danger' => 'unpaid',
                        'warning' => 'partially_paid',
                        'success' => 'settled',
                    ]),
                Tables\Columns\TextColumn::make('due_date')->date()->label('Jatuh Tempo'),
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
            'index' => Pages\ListDebts::route('/'),
            'create' => Pages\CreateDebt::route('/create'),
            'edit' => Pages\EditDebt::route('/{record}/edit'),
        ];
    }
}
