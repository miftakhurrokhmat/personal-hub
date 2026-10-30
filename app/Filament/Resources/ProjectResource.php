<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase'; // atau 'heroicon-o-clipboard-document-list'
    protected static ?string $navigationGroup = 'Relasi & Klien';
    protected static ?string $navigationLabel = 'Proyek & Penugasan';

    public static function shouldRegisterNavigation(): bool
    {
        $role = session('active_role', 'all');
        return in_array($role, ['all', 'manajer']);
    }

    public static function form(Form $form): Form
    {
        // return $form
        //     ->schema([
        //         Forms\Components\TextInput::make('client_id')
        //             ->numeric()
        //             ->default(null),
        //         Forms\Components\TextInput::make('title')
        //             ->required()
        //             ->maxLength(255),
        //         Forms\Components\TextInput::make('role_context')
        //             ->required(),
        //         Forms\Components\TextInput::make('status')
        //             ->required(),
        //         Forms\Components\TextInput::make('contract_value')
        //             ->required()
        //             ->numeric()
        //             ->default(0.00),
        //         Forms\Components\DatePicker::make('start_date'),
        //         Forms\Components\DatePicker::make('due_date'),
        //         Forms\Components\Textarea::make('description')
        //             ->columnSpanFull(),
        //     ]);

        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Proyek & Penugasan')
                    ->schema([
                        Forms\Components\Select::make('client_id')
                            ->relationship('client', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->label('Klien / Institusi Terkait'),
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('misal: Asesmen BNSP Batch 3 / Dev SIMRS / Workshop AI')
                            ->label('Nama Pekerjaan / Proyek'),
                        Forms\Components\Select::make('role_context')
                            ->options([
                                'dosen_kampus' => 'Dosen / Kampus',
                                'asesor' => 'Asesor LSP',
                                'trainer' => 'Trainer / Workshop',
                                'it_sysadmin' => 'Remote Sysadmin',
                                'web_dev' => 'Software / Web Development',
                            ])
                            ->required()
                            ->label('Peran / Bidang'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'prospek' => 'Prospek / Penawaran',
                                'aktif' => 'Sedang Dikerjakan (Aktif)',
                                'on_hold' => 'Tertunda (On Hold)',
                                'selesai' => 'Selesai',
                            ])
                            ->default('aktif')
                            ->required()
                            ->label('Status Proyek'),
                    ])->columns(2),

                Forms\Components\Section::make('Finansial & Tenggat Waktu')
                    ->schema([
                        Forms\Components\TextInput::make('contract_value')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->label('Nilai Kontrak / Honor Disepakati'),
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Tanggal Mulai'),
                        Forms\Components\DatePicker::make('due_date')
                            ->label('Tenggat Selesai (Deadline)'),
                        Forms\Components\Textarea::make('description')
                            ->nullable()
                            ->columnSpanFull()
                            ->label('Deskripsi / Cakupan Kerja'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('role_context'),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('contract_value')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
            RelationManagers\TasksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
