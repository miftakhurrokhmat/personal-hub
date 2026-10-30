<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MonthlyTargetResource\Pages;
use App\Filament\Resources\MonthlyTargetResource\RelationManagers;
use App\Models\MonthlyTarget;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MonthlyTargetResource extends Resource
{
    protected static ?string $model = MonthlyTarget::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation'; // atau 'heroicon-o-chart-bar'
    protected static ?string $navigationGroup = 'Keuangan & Kas';
    protected static ?string $navigationLabel = 'Target & Batasan Anggaran';

    public static function shouldRegisterNavigation(): bool
    {
        $role = session('active_role', 'all');
        return in_array($role, ['all', 'manajer']);
    }

    public static function form(Form $form): Form
    {
        // return $form
        //     ->schema([
        //         Forms\Components\TextInput::make('period')
        //             ->required()
        //             ->maxLength(255),
        //         Forms\Components\TextInput::make('income_target')
        //             ->required()
        //             ->numeric(),
        //         Forms\Components\TextInput::make('max_expense_limit')
        //             ->required()
        //             ->numeric(),
        //         Forms\Components\TextInput::make('weekly_work_cap_hours')
        //             ->required()
        //             ->numeric()
        //             ->default(40),
        //     ]);

        return $form
            ->schema([
                Forms\Components\Section::make('Pengaturan Target & Rem Hidup')
                    ->schema([
                        Forms\Components\TextInput::make('period')
                            ->required()
                            ->placeholder('YYYY-MM, misal: 2026-10')
                            ->label('Bulan & Tahun Periode'),
                        Forms\Components\TextInput::make('income_target')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->label('Target Pemasukan Bulanan'),
                        Forms\Components\TextInput::make('max_expense_limit')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->label('Batas Maksimal Belanja (Rem)'),
                        Forms\Components\TextInput::make('weekly_work_cap_hours')
                            ->numeric()
                            ->default(40)
                            ->suffix('Jam/Minggu')
                            ->required()
                            ->label('Batas Maksimal Jam Kerja'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        // return $table
        //     ->columns([
        //         Tables\Columns\TextColumn::make('period')
        //             ->searchable(),
        //         Tables\Columns\TextColumn::make('income_target')
        //             ->numeric()
        //             ->sortable(),
        //         Tables\Columns\TextColumn::make('max_expense_limit')
        //             ->numeric()
        //             ->sortable(),
        //         Tables\Columns\TextColumn::make('weekly_work_cap_hours')
        //             ->numeric()
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
                Tables\Columns\TextColumn::make('period')->label('Periode')->sortable(),
                Tables\Columns\TextColumn::make('income_target')->money('IDR')->label('Target Pemasukan'),
                Tables\Columns\TextColumn::make('max_expense_limit')->money('IDR')->label('Limit Belanja (Rem)'),
                Tables\Columns\TextColumn::make('weekly_work_cap_hours')->suffix(' Jam')->label('Max Kerja/Minggu'),
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
            'index' => Pages\ListMonthlyTargets::route('/'),
            'create' => Pages\CreateMonthlyTarget::route('/create'),
            'edit' => Pages\EditMonthlyTarget::route('/{record}/edit'),
        ];
    }
}
