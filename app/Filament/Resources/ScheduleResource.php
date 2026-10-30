<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScheduleResource\Pages;
use App\Filament\Resources\ScheduleResource\RelationManagers;
use App\Models\Schedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ScheduleResource extends Resource
{
    protected static ?string $model = Schedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Aktivitas & Waktu';
    protected static ?string $navigationLabel = 'Jadwal & Agenda';

    public static function shouldRegisterNavigation(): bool
    {
        $role = session('active_role', 'all');
        return in_array($role, ['all', 'warga']);
    }

    public static function form(Form $form): Form
    {
        // return $form
        //     ->schema([
        //         Forms\Components\Select::make('project_id')
        //             ->relationship('project', 'title')
        //             ->default(null),
        //         Forms\Components\Select::make('city_id')
        //             ->relationship('city', 'name')
        //             ->default(null),
        //         Forms\Components\TextInput::make('title')
        //             ->required()
        //             ->maxLength(255),
        //         Forms\Components\TextInput::make('category')
        //             ->required(),
        //         Forms\Components\DateTimePicker::make('start_time')
        //             ->required(),
        //         Forms\Components\DateTimePicker::make('end_time')
        //             ->required(),
        //         Forms\Components\TextInput::make('duration_hours')
        //             ->required()
        //             ->numeric()
        //             ->default(1),
        //         Forms\Components\TextInput::make('location_name')
        //             ->maxLength(255)
        //             ->default(null),
        //     ]);

        return $form
            ->schema([
                Forms\Components\Section::make('Kategori & Konteks Kegiatan')
                    ->schema([
                        Forms\Components\Select::make('category')
                            ->options([
                                'pekerjaan' => '💼 Pekerjaan (Kampus / Asesor / IT / Training)',
                                'desa' => '🌾 Kegiatan Warga Desa (Rapat RT / Sambatan / Sosial)',
                                'keluarga' => '👨‍👩‍👧‍👦 Agenda Keluarga',
                                'pribadi' => '☕ Waktu Pribadi / Istirahat / Hobi',
                            ])
                            ->default('pekerjaan')
                            ->required()
                            ->live()
                            ->label('Kategori Agenda'),

                        // Field proyek hanya muncul jika kategori = pekerjaan
                        Forms\Components\Select::make('project_id')
                            ->relationship('project', 'title')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->label('Terkait Proyek / Pekerjaan')
                            ->visible(fn(Forms\Get $get) => $get('category') === 'pekerjaan'),

                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('misal: Rapat Senat Kampus / Kerja Bakti RT 02 / Antar Anak')
                            ->label('Nama Kegiatan')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Waktu, Durasi & Lokasi')
                    ->schema([
                        Forms\Components\DateTimePicker::make('start_time')
                            ->required()
                            ->default(now())
                            ->label('Waktu Mulai'),
                        Forms\Components\DateTimePicker::make('end_time')
                            ->required()
                            ->default(now()->addHour())
                            ->label('Waktu Selesai'),
                        Forms\Components\TextInput::make('duration_hours')
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->suffix('Jam')
                            ->label('Estimasi Beban Waktu')
                            ->helperText('Berapa jam waktu/tenaga yang terkuras untuk agenda ini'),
                        Forms\Components\Select::make('city_id')
                            ->relationship('city', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->label('Kota / Kabupaten Lokasi'),
                        Forms\Components\TextInput::make('location_name')
                            ->nullable()
                            ->maxLength(255)
                            ->placeholder('misal: Balai RT 03 / Kampus Gedung B / Zoom Meeting')
                            ->label('Keterangan Tempat / Link'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        // return $table
        //     ->columns([
        //         Tables\Columns\TextColumn::make('project.title')
        //             ->numeric()
        //             ->sortable(),
        //         Tables\Columns\TextColumn::make('city.name')
        //             ->numeric()
        //             ->sortable(),
        //         Tables\Columns\TextColumn::make('title')
        //             ->searchable(),
        //         Tables\Columns\TextColumn::make('category'),
        //         Tables\Columns\TextColumn::make('start_time')
        //             ->dateTime()
        //             ->sortable(),
        //         Tables\Columns\TextColumn::make('end_time')
        //             ->dateTime()
        //             ->sortable(),
        //         Tables\Columns\TextColumn::make('duration_hours')
        //             ->numeric()
        //             ->sortable(),
        //         Tables\Columns\TextColumn::make('location_name')
        //             ->searchable(),
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
            ->defaultSort('start_time', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('start_time')
                    ->dateTime('d M Y H:i')
                    ->label('Waktu')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('category')
                    ->label('Kategori')
                    ->colors([
                        'primary' => 'pekerjaan',
                        'success' => 'desa',
                        'warning' => 'keluarga',
                        'gray' => 'pribadi',
                    ]),
                Tables\Columns\TextColumn::make('title')
                    ->label('Kegiatan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('duration_hours')
                    ->suffix(' Jam')
                    ->label('Durasi'),
                Tables\Columns\TextColumn::make('city.name')
                    ->label('Kota')
                    ->default('-'),
                Tables\Columns\TextColumn::make('project.title')
                    ->label('Proyek Terkait')
                    ->default('-'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'pekerjaan' => 'Pekerjaan',
                        'desa' => 'Kegiatan Desa',
                        'keluarga' => 'Keluarga',
                        'pribadi' => 'Pribadi',
                    ])
                    ->label('Filter Kategori'),
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
            'index' => Pages\ListSchedules::route('/'),
            'create' => Pages\CreateSchedule::route('/create'),
            'edit' => Pages\EditSchedule::route('/{record}/edit'),
        ];
    }
}
