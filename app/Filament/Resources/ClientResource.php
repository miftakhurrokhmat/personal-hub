<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Relasi & Klien';

    public static function shouldRegisterNavigation(): bool
    {
        $role = session('active_role', 'all');
        return in_array($role, ['all', 'manajer']);
    }

    public static function form(Form $form): Form
    {
        // return $form
        //     ->schema([
        //         Forms\Components\TextInput::make('city_id')
        //             ->numeric()
        //             ->default(null),
        //         Forms\Components\TextInput::make('name')
        //             ->required()
        //             ->maxLength(255),
        //         Forms\Components\TextInput::make('institution')
        //             ->maxLength(255)
        //             ->default(null),
        //         Forms\Components\Textarea::make('address')
        //             ->columnSpanFull(),
        //         Forms\Components\TextInput::make('latitude')
        //             ->numeric()
        //             ->default(null),
        //         Forms\Components\TextInput::make('longitude')
        //             ->numeric()
        //             ->default(null),
        //         Forms\Components\TextInput::make('category')
        //             ->required(),
        //         Forms\Components\TextInput::make('contact_person')
        //             ->maxLength(255)
        //             ->default(null),
        //         Forms\Components\TextInput::make('phone')
        //             ->tel()
        //             ->maxLength(255)
        //             ->default(null),
        //         Forms\Components\Textarea::make('notes')
        //             ->columnSpanFull(),
        //     ]);

        return $form
            ->schema([
                Forms\Components\Section::make('Profil Klien / Instansi')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nama Klien / Mitra'),
                        Forms\Components\TextInput::make('institution')
                            ->nullable()
                            ->maxLength(255)
                            ->placeholder('misal: Universitas X / LSP Y / Pemdes Z')
                            ->label('Institusi / Perusahaan'),
                        Forms\Components\Select::make('category')
                            ->options([
                                'kampus' => 'Kampus / Perguruan Tinggi',
                                'lsp' => 'Lembaga Sertifikasi Profesi (LSP)',
                                'mitra_training' => 'Mitra Training / Korporat',
                                'klien_it' => 'Klien Proyek IT / Software House',
                                'desa' => 'Pemerintah Desa / Warga',
                                'lainnya' => 'Lainnya',
                            ])
                            ->required()
                            ->label('Kategori'),
                        Forms\Components\Select::make('city_id')
                            ->relationship('city', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->label('Kota / Kabupaten'),
                        Forms\Components\Textarea::make('address')
                            ->nullable()
                            ->columnSpanFull()
                            ->label('Alamat Lengkap'),
                    ])->columns(2),

                Forms\Components\Section::make('Kontak & Titik Lokasi')
                    ->schema([
                        Forms\Components\TextInput::make('contact_person')
                            ->nullable()
                            ->maxLength(255)
                            ->label('Nama PIC / Kontak Person'),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->nullable()
                            ->maxLength(255)
                            ->label('No. Telepon / WhatsApp'),
                        Forms\Components\TextInput::make('latitude')
                            ->numeric()
                            ->nullable()
                            ->label('Latitude Koordinat'),
                        Forms\Components\TextInput::make('longitude')
                            ->numeric()
                            ->nullable()
                            ->label('Longitude Koordinat'),
                        Forms\Components\Textarea::make('notes')
                            ->nullable()
                            ->columnSpanFull()
                            ->label('Catatan Khusus'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('city_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('institution')
                    ->searchable(),
                Tables\Columns\TextColumn::make('latitude')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('longitude')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category'),
                Tables\Columns\TextColumn::make('contact_person')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
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
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
