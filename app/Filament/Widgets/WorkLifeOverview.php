<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Schedule;
use App\Models\MonthlyTarget;
use Carbon\Carbon;

class WorkLifeOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        $currentPeriod = Carbon::now()->format('Y-m');

        $target = MonthlyTarget::where('period', $currentPeriod)->first();
        $weeklyCap = $target ? $target->weekly_work_cap_hours : 40;

        // 1. Total jam kerja produktif pekan ini
        $workHours = Schedule::whereBetween('start_time', [$startOfWeek, $endOfWeek])
            ->where('category', 'pekerjaan')
            ->sum('duration_hours');

        // 2. Total jam kegiatan desa
        $villageHours = Schedule::whereBetween('start_time', [$startOfWeek, $endOfWeek])
            ->where('category', 'desa')
            ->sum('duration_hours');

        // 3. Total jam keluarga & istirahat pribadi
        $familyHours = Schedule::whereBetween('start_time', [$startOfWeek, $endOfWeek])
            ->whereIn('category', ['keluarga', 'pribadi'])
            ->sum('duration_hours');

        $isOverwork = $workHours > $weeklyCap;
        $workColor = $isOverwork ? 'danger' : ($workHours >= ($weeklyCap * 0.85) ? 'warning' : 'success');
        $workStatus = $isOverwork 
            ? 'BAHAYA: Melebihi batas mingguan!' 
            : ($workHours >= ($weeklyCap * 0.85) ? 'Mendekati limit jam kerja' : 'Kapasitas kerja ideal');

        return [
            Stat::make('Jam Kerja Pekan Ini', "{$workHours} / {$weeklyCap} Jam")
                ->description($workStatus)
                ->descriptionIcon($isOverwork ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-badge')
                ->color($workColor),

            Stat::make('Sosial Warga Desa', "{$villageHours} Jam")
                ->description($villageHours > 0 ? 'Aktif bermasyarakat' : 'Belum ada agenda desa')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('info'),

            Stat::make('Keluarga & Istirahat', "{$familyHours} Jam")
                ->description($familyHours < 15 ? 'Perlu tambah waktu rehat' : 'Waktu domestik terjaga')
                ->descriptionIcon('heroicon-m-home')
                ->color($familyHours < 15 ? 'warning' : 'success'),
        ];
    }
}