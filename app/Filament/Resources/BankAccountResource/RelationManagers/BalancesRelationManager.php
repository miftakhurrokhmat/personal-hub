<?php

namespace App\Filament\Resources\BankAccountResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BalancesRelationManager extends RelationManager
{
    protected static string $relationship = 'balances';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('recorded_date')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        // return $table
        //     ->recordTitleAttribute('recorded_date')
        //     ->columns([
        //         Tables\Columns\TextColumn::make('recorded_date'),
        //     ])
        //     ->filters([
        //         //
        //     ])
        //     ->headerActions([
        //         Tables\Actions\CreateAction::make(),
        //     ])
        //     ->actions([
        //         Tables\Actions\EditAction::make(),
        //         Tables\Actions\DeleteAction::make(),
        //     ])
        //     ->bulkActions([
        //         Tables\Actions\BulkActionGroup::make([
        //             Tables\Actions\DeleteBulkAction::make(),
        //         ]),
        //     ]);

        return $table
            ->recordTitleAttribute('recorded_date')
            ->columns([
                Tables\Columns\TextColumn::make('recorded_date')
                    ->label('Tanggal Dicatat')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('balance')
                    ->label('Nominal Saldo')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('note')
                    ->label('Keterangan'),
            ])
            ->defaultSort('id', 'desc');
    }
}
