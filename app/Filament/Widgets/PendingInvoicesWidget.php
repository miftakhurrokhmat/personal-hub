<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Income;
use Carbon\Carbon;

class PendingInvoicesWidget extends BaseWidget
{
    protected static ?string $heading = '⏳ Daftar Tagihan & Honor Menunggu Pembayaran';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Income::query()
                    ->whereIn('payment_status', ['draft', 'billed'])
                    ->orderBy('due_date', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Uraian Tagihan / Proyek')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('client.name')
                    ->label('Klien / Institusi')
                    ->default('-'),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Nominal')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('days_remaining')
                    ->label('Sisa Waktu')
                    ->badge()
                    ->state(function (Income $record): string {
                        if (!$record->due_date) {
                            return 'Belum Ditentukan';
                        }

                        $today = Carbon::now()->startOfDay();
                        $dueDate = Carbon::parse($record->due_date)->startOfDay();
                        $diff = $today->diffInDays($dueDate, false);

                        if ($diff < 0) {
                            return 'Lewat ' . abs((int)$diff) . ' Hari!';
                        } elseif ($diff === 0) {
                            return 'Hari Ini Jatuh Tempo';
                        } else {
                            return "Tersisa {$diff} Hari";
                        }
                    })
                    ->color(function (Income $record): string {
                        if (!$record->due_date) {
                            return 'gray';
                        }
                        $diff = Carbon::now()->startOfDay()->diffInDays(Carbon::parse($record->due_date)->startOfDay(), false);

                        if ($diff < 0) return 'danger';
                        if ($diff <= 3) return 'warning';
                        return 'success';
                    }),

                Tables\Columns\BadgeColumn::make('payment_status')
                    ->label('Status')
                    ->colors([
                        'gray' => 'draft',
                        'danger' => 'billed',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('markAsPaid')
                    ->label('Tandai Lunas')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn(Income $record) => $record->update([
                        'payment_status' => 'paid',
                        'received_at' => now(),
                    ])),
            ]);
    }
}
