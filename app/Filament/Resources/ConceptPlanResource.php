<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConceptPlanResource\Pages;
use App\Filament\Resources\ConceptPlanResource\RelationManagers;
use App\Models\ConceptPlan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ConceptPlanResource extends Resource
{
    protected static ?string $model = ConceptPlan::class;

    protected static ?string $navigationGroup = 'Aktivitas & Waktu';
    protected static ?string $navigationLabel = 'Konsep & Ide Rencana';
    protected static ?string $modelLabel = 'Konsep Rencana';
    protected static ?string $pluralModelLabel = 'Konsep & Ide Rencana';
    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';

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
        //         Forms\Components\TextInput::make('title')
        //             ->required()
        //             ->maxLength(255),
        //         Forms\Components\TextInput::make('category')
        //             ->required(),
        //         Forms\Components\Textarea::make('description')
        //             ->columnSpanFull(),
        //         Forms\Components\TextInput::make('status')
        //             ->required(),
        //     ]);

        return $form
            ->schema([
                Forms\Components\Section::make('Rincian Ide / Rencana')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->label('Judul Konsep / Rencana')
                            ->placeholder('misal: Rencana Workshop AI Guru / Pelatihan IoT Desa'),
                        Forms\Components\Select::make('category')
                            ->options([
                                'pekerjaan' => 'Pekerjaan',
                                'desa' => 'Kegiatan Warga Desa',
                                'keluarga' => 'Keluarga',
                                'pribadi' => 'Pribadi',
                            ])
                            ->default('pekerjaan')
                            ->required()
                            ->label('Kategori'),
                        Forms\Components\Select::make('project_id')
                            ->relationship('project', 'title')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->label('Terkait Proyek (Opsional)'),
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft Ide',
                                'review' => 'Dalam Pertimbangan',
                                'terealisasi' => 'Sudah Masuk Jadwal',
                                'dibatalkan' => 'Dibatalkan',
                            ])
                            ->default('draft')
                            ->required()
                            ->label('Status'),
                        Forms\Components\Textarea::make('description')
                            ->rows(4)
                            ->columnSpanFull()
                            ->label('Catatan Ide & Rencana Tindakan'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        // return $table
        //     ->columns([
        //         Tables\Columns\TextColumn::make('project.title')
        //             ->numeric()
        //             ->sortable(),
        //         Tables\Columns\TextColumn::make('title')
        //             ->searchable(),
        //         Tables\Columns\TextColumn::make('category'),
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
                Tables\Columns\TextColumn::make('title')->label('Judul Konsep')->searchable()->weight('bold'),
                Tables\Columns\BadgeColumn::make('category')->label('Kategori'),
                Tables\Columns\TextColumn::make('project.title')->label('Proyek')->default('-'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'gray' => 'draft',
                        'warning' => 'review',
                        'success' => 'terealisasi',
                        'danger' => 'dibatalkan',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                // Action Satu Klik: Realisasikan Konsep Menjadi Jadwal
                Tables\Actions\Action::make('realizeToSchedule')
                    ->label('Jadikan Jadwal')
                    ->icon('heroicon-m-calendar-days')
                    ->color('success')
                    ->visible(fn($record) => $record->status !== 'terealisasi')
                    ->form([
                        Forms\Components\DateTimePicker::make('start_time')
                            ->label('Waktu Mulai')
                            ->required()
                            ->default(now()),
                        Forms\Components\DateTimePicker::make('end_time')
                            ->label('Waktu Selesai')
                            ->required()
                            ->default(now()->addHour(2)),
                        Forms\Components\TextInput::make('duration_hours')
                            ->numeric()
                            ->default(2)
                            ->required()
                            ->suffix('Jam')
                            ->label('Beban Jam'),
                    ])
                    ->action(function ($record, array $data) {
                        Schedule::create([
                            'title' => $record->title,
                            'category' => $record->category,
                            'project_id' => $record->project_id,
                            'start_time' => $data['start_time'],
                            'end_time' => $data['end_time'],
                            'duration_hours' => $data['duration_hours'],
                            'location_name' => 'Dari Rencana: ' . $record->title,
                        ]);

                        $record->update(['status' => 'terealisasi']);

                        Notification::make()
                            ->title('Berhasil Dijadwalkan')
                            ->body("Konsep '{$record->title}' sudah otomatis masuk ke kalender kegiatan!")
                            ->success()
                            ->send();
                    }),
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
            'index' => Pages\ListConceptPlans::route('/'),
            'create' => Pages\CreateConceptPlan::route('/create'),
            'edit' => Pages\EditConceptPlan::route('/{record}/edit'),
        ];
    }
}
