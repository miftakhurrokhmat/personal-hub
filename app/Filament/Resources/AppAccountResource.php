<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppAccountResource\Pages;
use App\Filament\Resources\AppAccountResource\RelationManagers;
use App\Models\AppAccount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AppAccountResource extends Resource
{
    protected static ?string $model = AppAccount::class;

    protected static ?string $navigationIcon = 'heroicon-o-lock-closed'; // atau 'heroicon-o-key'
    protected static ?string $navigationGroup = 'Keamanan & Akses';
    protected static ?string $navigationLabel = 'Brankas Akun (Vault)';

    public static function shouldRegisterNavigation(): bool
    {
        $role = session('active_role', 'all');
        return in_array($role, ['all', 'developer']);
    }

    public static function form(Form $form): Form
    {
        // return $form
        //     ->schema([
        //         Forms\Components\Select::make('client_id')
        //             ->relationship('client', 'name')
        //             ->default(null),
        //         Forms\Components\Select::make('project_id')
        //             ->relationship('project', 'title')
        //             ->default(null),
        //         Forms\Components\TextInput::make('service_name')
        //             ->required()
        //             ->maxLength(255),
        //         Forms\Components\TextInput::make('host_or_url')
        //             ->required()
        //             ->maxLength(255),
        //         Forms\Components\TextInput::make('port')
        //             ->numeric()
        //             ->default(null),
        //         Forms\Components\TextInput::make('protocol_type')
        //             ->required(),
        //         Forms\Components\TextInput::make('username')
        //             ->required()
        //             ->maxLength(255),
        //         Forms\Components\Textarea::make('password')
        //             ->required()
        //             ->columnSpanFull(),
        //         Forms\Components\Textarea::make('notes')
        //             ->columnSpanFull(),
        //     ]);

        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Layanan')
                    ->schema([
                        Forms\Components\Select::make('client_id')
                            ->relationship('client', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->label('Terkait Klien/Mitra'),
                        Forms\Components\Select::make('project_id')
                            ->relationship('project', 'title')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->label('Terkait Proyek'),
                        Forms\Components\TextInput::make('service_name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('misal: Server Proxmox Node 1 / Portal BKD / cPanel')
                            ->label('Nama Layanan / Akun'),
                        Forms\Components\Select::make('protocol_type')
                            ->options([
                                'ssh' => 'SSH',
                                'web_http' => 'Web (HTTP/HTTPS)',
                                'database' => 'Database (MySQL/PostgreSQL)',
                                'panel_cpanel' => 'Control Panel (cPanel/CloudPanel)',
                                'api_service' => 'API Token / Key',
                                'lainnya' => 'Lainnya',
                            ])
                            ->default('web_http')
                            ->required()
                            ->label('Jenis Protokol / Akses'),
                    ])->columns(2),

                Forms\Components\Section::make('Kredensial & Endpoint Akses')
                    ->schema([
                        Forms\Components\TextInput::make('host_or_url')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('misal: 192.168.1.100 atau https://kampus.ac.id')
                            ->label('Host / IP / URL'),
                        Forms\Components\TextInput::make('port')
                            ->numeric()
                            ->nullable()
                            ->placeholder('misal: 22 / 3306 / 8006')
                            ->label('Port'),
                        Forms\Components\TextInput::make('username')
                            ->required()
                            ->maxLength(255)
                            ->label('Username / Email'),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->required()
                            ->label('Password (Terenkripsi)'),
                        Forms\Components\Textarea::make('secret_key_or_token')
                            ->rows(4)
                            ->nullable()
                            ->columnSpanFull()
                            ->label('Secret Key / Token / Private Key (Opsional)'),
                        Forms\Components\Textarea::make('notes')
                            ->nullable()
                            ->columnSpanFull()
                            ->label('Catatan Tambahan'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('project.title')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('service_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('host_or_url')
                    ->searchable(),
                Tables\Columns\TextColumn::make('port')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('protocol_type'),
                Tables\Columns\TextColumn::make('username')
                    ->searchable(),
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
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAppAccounts::route('/'),
            'create' => Pages\CreateAppAccount::route('/create'),
            'edit' => Pages\EditAppAccount::route('/{record}/edit'),
        ];
    }
}
