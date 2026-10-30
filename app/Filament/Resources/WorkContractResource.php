<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkContractResource\Pages;
use App\Filament\Resources\WorkContractResource\RelationManagers;
use App\Models\WorkContract;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WorkContractResource extends Resource
{
    protected static ?string $model = WorkContract::class;

    protected static ?string $navigationGroup = 'Dokumen & Legal';
    protected static ?string $navigationLabel = 'Surat Kontrak';
    protected static ?string $navigationIcon = 'heroicon-o-document-check';

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
        //         Forms\Components\TextInput::make('client_id')
        //             ->numeric()
        //             ->default(null),
        //         Forms\Components\TextInput::make('contract_number')
        //             ->required()
        //             ->maxLength(255),
        //         Forms\Components\TextInput::make('job_title')
        //             ->required()
        //             ->maxLength(255),
        //         Forms\Components\Textarea::make('scope_of_work')
        //             ->columnSpanFull(),
        //         Forms\Components\DatePicker::make('start_date')
        //             ->required(),
        //         Forms\Components\DatePicker::make('end_date')
        //             ->required(),
        //         Forms\Components\TextInput::make('contract_value')
        //             ->numeric()
        //             ->default(null),
        //         Forms\Components\TextInput::make('status')
        //             ->required(),
        //         Forms\Components\Textarea::make('notes')
        //             ->columnSpanFull(),
        //     ]);

        return $form
            ->schema([
                Forms\Components\Section::make('Identitas Kontrak & Pihak Terkait')
                    ->schema([
                        Forms\Components\TextInput::make('contract_number')
                            ->label('Nomor Kontrak/SPK')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->default(fn() => 'KTR/' . date('Y/m') . '/' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT))
                            ->placeholder('misal: KTR/2026/10/003'),

                        // Relasi ke Master Klien
                        Forms\Components\Select::make('client_id')
                            ->label('Klien / Institusi Mitra')
                            ->relationship('client', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->placeholder('Pilih mitra/klien'),

                        // Relasi ke Master Proyek
                        Forms\Components\Select::make('project_id')
                            ->label('Terkait Proyek')
                            ->relationship('project', 'title')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->placeholder('Pilih proyek'),

                        Forms\Components\TextInput::make('job_title')
                            ->label('Judul Pekerjaan / Lingkup Kontrak')
                            ->required()
                            ->placeholder('misal: Pengadaan & Setup Modul Integrasi SIMRS-Bank'),

                        Forms\Components\DatePicker::make('start_date')
                            ->label('Tanggal Berlaku')
                            ->required()
                            ->default(now()),

                        Forms\Components\DatePicker::make('end_date')
                            ->label('Tanggal Berakhir')
                            ->required()
                            ->default(now()->addMonths(1)),

                        // Nilai Kontrak (Bisa Kosong / Menyusul)
                        Forms\Components\TextInput::make('contract_value')
                            ->label('Nilai Kontrak (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->helperText('Boleh dikosongkan jika nilai nominal masih tentatif / menyusul.')
                            ->placeholder('0'),

                        Forms\Components\Select::make('status')
                            ->label('Status Kontrak')
                            ->options([
                                'draft' => 'Draft Dokumen',
                                'active' => 'Berjalan (Aktif)',
                                'completed' => 'Selesai',
                                'canceled' => 'Dibatalkan',
                            ])
                            ->default('draft')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Ruang Lingkup & Ketentuan')
                    ->schema([
                        Forms\Components\Textarea::make('scope_of_work')
                            ->label('Ruang Lingkup Kerja / Deliverables')
                            ->rows(4)
                            ->placeholder("1. Analisis requirement sistem dan arsitektur database.\n2. Deployment server dan uji koneksi payment gateway."),

                        Forms\Components\Textarea::make('notes')
                            ->label('Klausul Tambahan / Catatan Khusus')
                            ->rows(2)
                            ->placeholder('misal: Pembayaran dilakukan dalam 2 termin (DP 50% dan Pelunasan 50% setelah UAT).'),
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
        //         Tables\Columns\TextColumn::make('client_id')
        //             ->numeric()
        //             ->sortable(),
        //         Tables\Columns\TextColumn::make('contract_number')
        //             ->searchable(),
        //         Tables\Columns\TextColumn::make('job_title')
        //             ->searchable(),
        //         Tables\Columns\TextColumn::make('start_date')
        //             ->date()
        //             ->sortable(),
        //         Tables\Columns\TextColumn::make('end_date')
        //             ->date()
        //             ->sortable(),
        //         Tables\Columns\TextColumn::make('contract_value')
        //             ->numeric()
        //             ->sortable(),
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
            ->defaultSort('start_date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('contract_number')
                    ->label('No. Kontrak')
                    ->weight('bold')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('client.name')
                    ->label('Klien')
                    ->default('-')
                    ->searchable(),

                Tables\Columns\TextColumn::make('job_title')
                    ->label('Pekerjaan')
                    ->limit(30),

                Tables\Columns\TextColumn::make('contract_value')
                    ->label('Nilai Kontrak')
                    ->money('IDR')
                    ->default('Tentatif / Rp 0')
                    ->sortable(),

                Tables\Columns\TextColumn::make('start_date')
                    ->label('Durasi')
                    ->formatStateUsing(fn($record) => $record->start_date->format('d/m/Y') . ' - ' . $record->end_date->format('d/m/Y')),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'gray' => 'draft',
                        'success' => 'active',
                        'primary' => 'completed',
                        'danger' => 'canceled',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('print')
                    ->label('Cetak PDF')
                    ->icon('heroicon-o-printer')
                    ->color('success')
                    ->url(fn($record) => route('print.work-contract', $record))
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
            'index' => Pages\ListWorkContracts::route('/'),
            'create' => Pages\CreateWorkContract::route('/create'),
            'edit' => Pages\EditWorkContract::route('/{record}/edit'),
        ];
    }
}
