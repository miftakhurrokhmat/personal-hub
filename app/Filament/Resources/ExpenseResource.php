<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages;
use App\Filament\Resources\ExpenseResource\RelationManagers;
use App\Models\Expense;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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

        return $table
            ->defaultSort('spent_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('spent_at')->date()->label('Tanggal')->sortable(),
                Tables\Columns\TextColumn::make('title')->label('Keperluan')->searchable(),
                Tables\Columns\BadgeColumn::make('category')->label('Kategori'),
                Tables\Columns\TextColumn::make('amount')->money('IDR')->label('Nominal')->sortable(),
                Tables\Columns\TextColumn::make('bankAccount.name')->label('Sumber Dana')->default('-'),
                Tables\Columns\TextColumn::make('project.title')->label('Proyek')->default('-'),
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
