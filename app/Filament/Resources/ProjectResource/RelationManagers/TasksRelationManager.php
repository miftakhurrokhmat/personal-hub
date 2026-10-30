<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';
    protected static ?string $title = 'Tahapan & Daftar Tugas';

    public function form(Form $form): Form
    {
        // return $form
        //     ->schema([
        //         Forms\Components\TextInput::make('title')
        //             ->required()
        //             ->maxLength(255),
        //     ]);

        return $form
            ->schema([
                Forms\Components\TextInput::make('order_column')
                    ->numeric()
                    ->default(1)
                    ->required()
                    ->label('Tahap Ke-')
                    ->helperText('Urutan langkah, misal: 1, 2, 3'),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->label('Nama Tugas / Milestone'),
                Forms\Components\Select::make('status')
                    ->options([
                        'todo' => 'To Do (Antrean)',
                        'in_progress' => 'Sedang Dikerjakan',
                        'review' => 'Review / Pengujian',
                        'done' => 'Selesai',
                    ])
                    ->default('todo')
                    ->required()
                    ->label('Status'),
                Forms\Components\DatePicker::make('due_date')
                    ->label('Target Selesai'),
                Forms\Components\Textarea::make('notes')
                    ->nullable()
                    ->columnSpanFull()
                    ->label('Catatan Pengerjaan'),
            ])->columns(3);
    }

    public function table(Table $table): Table
    {
        // return $table
        //     ->recordTitleAttribute('title')
        //     ->columns([
        //         Tables\Columns\TextColumn::make('title'),
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
            ->recordTitleAttribute('title')
            ->defaultSort('order_column', 'asc') // Urut dari tahap 1, 2, dst.
            ->columns([
                Tables\Columns\TextColumn::make('order_column')
                    ->label('Tahap')
                    ->badge()
                    ->color('gray')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Tugas / Kegiatan')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray' => 'todo',
                        'warning' => 'in_progress',
                        'info' => 'review',
                        'success' => 'done',
                    ]),
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->label('Deadline'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('Tambah Tugas'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
