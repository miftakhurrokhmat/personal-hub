<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssignmentLetterResource\Pages;
use App\Filament\Resources\AssignmentLetterResource\RelationManagers;
use App\Models\AssignmentLetter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AssignmentLetterResource extends Resource
{
    protected static ?string $model = AssignmentLetter::class;

    protected static ?string $navigationGroup = 'Dokumen & Legal';
    protected static ?string $navigationLabel = 'Surat Penugasan Tim';
    protected static ?string $navigationIcon = 'heroicon-o-identification';

    public static function shouldRegisterNavigation(): bool
    {
        $role = session('active_role', 'all');
        return in_array($role, ['all', 'manajer']);
    }

    public static function form(Form $form): Form
    {
        // return $form
        //     ->schema([
        //         Forms\Components\TextInput::make('project_id')
        //             ->numeric()
        //             ->default(null),
        //         Forms\Components\TextInput::make('letter_number')
        //             ->required()
        //             ->maxLength(255),
        //         Forms\Components\TextInput::make('assignment_title')
        //             ->required()
        //             ->maxLength(255),
        //         Forms\Components\Textarea::make('assigned_personnel')
        //             ->required()
        //             ->columnSpanFull(),
        //         Forms\Components\Textarea::make('assignment_duty')
        //             ->required()
        //             ->columnSpanFull(),
        //         Forms\Components\DatePicker::make('start_date')
        //             ->required(),
        //         Forms\Components\DatePicker::make('end_date')
        //             ->required(),
        //         Forms\Components\TextInput::make('location')
        //             ->maxLength(255)
        //             ->default(null),
        //     ]);

        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Surat Penugasan')
                    ->description('Rincian administrasi dan nomor surat tugas tim')
                    ->schema([
                        Forms\Components\TextInput::make('letter_number')
                            ->label('Nomor Surat Tugas')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->default(fn() => 'ST/' . date('Y/m') . '/' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT))
                            ->placeholder('misal: ST/2026/10/015'),

                        // Relasi ke Master Proyek
                        Forms\Components\Select::make('project_id')
                            ->label('Terkait Proyek')
                            ->relationship('project', 'title')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->placeholder('Pilih proyek (opsional)'),

                        Forms\Components\TextInput::make('assignment_title')
                            ->label('Perihal / Tugas Utama')
                            ->required()
                            ->columnSpanFull()
                            ->placeholder('misal: Penugasan Tim Penguji Asesmen LSP Batch 4'),

                        Forms\Components\DatePicker::make('start_date')
                            ->label('Tanggal Mulai')
                            ->required()
                            ->default(now()),

                        Forms\Components\DatePicker::make('end_date')
                            ->label('Tanggal Selesai')
                            ->required()
                            ->default(now()->addDays(2)),

                        Forms\Components\TextInput::make('location')
                            ->label('Lokasi Penugasan')
                            ->placeholder('misal: Gedung Graha Sarana / Daring via Zoom')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Rincian Personel & Uraian Tanggung Jawab')
                    ->schema([
                        Forms\Components\Textarea::make('assigned_personnel')
                            ->label('Personel / Anggota Tim yang Ditugaskan')
                            ->required()
                            ->rows(3)
                            ->helperText('Tuliskan nama anggota tim beserta peran/posisi.')
                            ->placeholder("1. Miftakhur Rokhmat (Lead Assessor)\n2. Nama Anggota (Teknisi)"),

                        Forms\Components\Textarea::make('assignment_duty')
                            ->label('Rincian Instruksi / Tugas & Wewenang')
                            ->required()
                            ->rows(4)
                            ->placeholder("1. Melaksanakan verifikasi berkas portofolio asesi.\n2. Mengawasi uji kompetensi teknis dan menyusun laporan rekapitulasi nilai."),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        // return $table
        //     ->columns([
        //         Tables\Columns\TextColumn::make('project_id')
        //             ->numeric()
        //             ->sortable(),
        //         Tables\Columns\TextColumn::make('letter_number')
        //             ->searchable(),
        //         Tables\Columns\TextColumn::make('assignment_title')
        //             ->searchable(),
        //         Tables\Columns\TextColumn::make('start_date')
        //             ->date()
        //             ->sortable(),
        //         Tables\Columns\TextColumn::make('end_date')
        //             ->date()
        //             ->sortable(),
        //         Tables\Columns\TextColumn::make('location')
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
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('letter_number')
                    ->label('No. Surat')
                    ->searchable()
                    ->weight('bold')
                    ->copyable(),

                Tables\Columns\TextColumn::make('assignment_title')
                    ->label('Perihal Tugas')
                    ->searchable()
                    ->limit(35),

                Tables\Columns\TextColumn::make('project.title')
                    ->label('Proyek')
                    ->default('-')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Periode')
                    ->formatStateUsing(fn($record) => $record->start_date->format('d M') . ' - ' . $record->end_date->format('d M Y')),

                Tables\Columns\TextColumn::make('location')
                    ->label('Lokasi')
                    ->default('-'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('print')
                    ->label('Cetak PDF')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn($record) => route('print.assignment-letter', $record))
                    ->openUrlInNewTab(),
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
            'index' => Pages\ListAssignmentLetters::route('/'),
            'create' => Pages\CreateAssignmentLetter::route('/create'),
            'edit' => Pages\EditAssignmentLetter::route('/{record}/edit'),
        ];
    }
}
