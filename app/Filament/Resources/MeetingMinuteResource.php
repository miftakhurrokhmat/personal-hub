<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MeetingMinuteResource\Pages;
use App\Filament\Resources\MeetingMinuteResource\RelationManagers;
use App\Models\MeetingMinute;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Get;
use Filament\Forms\Set;
use App\Models\Task;

class MeetingMinuteResource extends Resource
{
    protected static ?string $model = MeetingMinute::class;

    protected static ?string $navigationGroup = 'Aktivitas & Waktu';
    protected static ?string $navigationLabel = 'Notulensi Rapat';
    protected static ?string $modelLabel = 'Notulensi Rapat';
    protected static ?string $pluralModelLabel = 'Notulensi Rapat';
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    public static function shouldRegisterNavigation(): bool
    {
        $role = session('active_role', 'all');
        return in_array($role, ['all', 'manajer']);
    }

    public static function form(Form $form): Form
    {
        // return $form
        //     ->schema([
        //         Forms\Components\Select::make('project_id')
        //             ->relationship('project', 'title')
        //             ->default(null),
        //         Forms\Components\Select::make('task_id')
        //             ->relationship('task', 'title')
        //             ->default(null),
        //         Forms\Components\TextInput::make('title')
        //             ->required()
        //             ->maxLength(255),
        //         Forms\Components\DateTimePicker::make('meeting_date')
        //             ->required(),
        //         Forms\Components\TextInput::make('location_or_link')
        //             ->maxLength(255)
        //             ->default(null),
        //         Forms\Components\Textarea::make('attendees')
        //             ->columnSpanFull(),
        //         Forms\Components\Textarea::make('discussion_notes')
        //             ->required()
        //             ->columnSpanFull(),
        //         Forms\Components\Textarea::make('action_plans')
        //             ->columnSpanFull(),
        //     ]);

        return $form
            ->schema([
                // Forms\Components\Section::make('Informasi Rapat')
                //     ->schema([
                //         Forms\Components\TextInput::make('title')
                //             ->required()
                //             ->label('Topik / Agenda Rapat')
                //             ->placeholder('misal: Koordinasi LSP Batch 3 / Rapat Pembahasan Anggaran Desa'),
                //         Forms\Components\DateTimePicker::make('meeting_date')
                //             ->required()
                //             ->default(now())
                //             ->label('Waktu & Tanggal Rapat'),
                //         Forms\Components\Select::make('project_id')
                //             ->relationship('project', 'title')
                //             ->searchable()
                //             ->preload()
                //             ->nullable()
                //             ->label('Terkait Proyek (Opsional)'),
                //         Forms\Components\TextInput::make('location_or_link')
                //             ->nullable()
                //             ->label('Lokasi / Tautan Meeting')
                //             ->placeholder('misal: Ruang Sidang Utama / Google Meet link'),
                //         Forms\Components\Textarea::make('attendees')
                //             ->rows(2)
                //             ->columnSpanFull()
                //             ->label('Daftar Hadir / Peserta Rapat'),
                //     ])->columns(2),
                Forms\Components\Section::make('Informasi Rapat')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->label('Topik / Agenda Rapat')
                            ->placeholder('misal: Koordinasi LSP Batch 3 / Rapat Pembahasan Anggaran Desa'),

                        Forms\Components\DateTimePicker::make('meeting_date')
                            ->required()
                            ->default(now())
                            ->label('Waktu & Tanggal Rapat'),

                        // 1. Dropdown Proyek (Reaktif)
                        Forms\Components\Select::make('project_id')
                            ->relationship('project', 'title')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->live() // atau ->reactive()
                            ->afterStateUpdated(fn(Set $set) => $set('task_id', null))
                            ->label('Terkait Proyek (Opsional)'),

                        // 2. Dropdown Task Dinamis (Tergantung Proyek)
                        Forms\Components\Select::make('task_id')
                            ->label('Terkait Tugas Spesifik')
                            ->options(function (Get $get) {
                                $projectId = $get('project_id');
                                if (! $projectId) {
                                    return [];
                                }
                                return Task::where('project_id', $projectId)
                                    ->pluck('title', 'id');
                            })
                            ->searchable()
                            ->nullable()
                            ->disabled(fn(Get $get) => empty($get('project_id')))
                            ->helperText('Pilih proyek terlebih dahulu untuk memunculkan daftar tugas.'),

                        Forms\Components\TextInput::make('location_or_link')
                            ->nullable()
                            ->label('Lokasi / Tautan Meeting')
                            ->placeholder('misal: Ruang Sidang Utama / Google Meet link'),

                        Forms\Components\Textarea::make('attendees')
                            ->rows(2)
                            ->columnSpanFull()
                            ->label('Daftar Hadir / Peserta Rapat'),
                    ])->columns(2),


                Forms\Components\Section::make('Hasil Diskusi & Kesepakatan')
                    ->schema([
                        Forms\Components\RichEditor::make('discussion_notes')
                            ->required()
                            ->columnSpanFull()
                            ->label('Poin-Poin Pembahasan / Diskusi'),
                        Forms\Components\Textarea::make('action_plans')
                            ->rows(3)
                            ->columnSpanFull()
                            ->label('Tindak Lanjut / Action Items'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        // return $table
        //     ->columns([
        //         Tables\Columns\TextColumn::make('project.title')
        //             ->numeric()
        //             ->sortable(),
        //         Tables\Columns\TextColumn::make('task.title')
        //             ->numeric()
        //             ->sortable(),
        //         Tables\Columns\TextColumn::make('title')
        //             ->searchable(),
        //         Tables\Columns\TextColumn::make('meeting_date')
        //             ->dateTime()
        //             ->sortable(),
        //         Tables\Columns\TextColumn::make('location_or_link')
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
            ->defaultSort('meeting_date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('meeting_date')->dateTime('d M Y H:i')->label('Tanggal')->sortable(),
                Tables\Columns\TextColumn::make('title')->label('Topik Rapat')->searchable()->weight('bold'),
                Tables\Columns\TextColumn::make('project.title')->label('Proyek Terkait')->default('-'),
                Tables\Columns\TextColumn::make('location_or_link')->label('Lokasi / Platform')->default('-'),
                Tables\Columns\TextColumn::make('task.title')
                    ->label('Tugas Spesifik')
                    ->default('-')
                    ->badge()
                    ->color('gray'),
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
            'index' => Pages\ListMeetingMinutes::route('/'),
            'create' => Pages\CreateMeetingMinute::route('/create'),
            'edit' => Pages\EditMeetingMinute::route('/{record}/edit'),
        ];
    }
}
