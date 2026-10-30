<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Schedule;
use Carbon\Carbon;

class MonthCalendarWidget extends Widget
{
    protected static string $view = 'filament.widgets.month-calendar-widget';
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    public string $currentMonth;

    public function mount(): void
    {
        $this->currentMonth = Carbon::now()->format('Y-m');
    }

    public function prevMonth(): void
    {
        $this->currentMonth = Carbon::parse($this->currentMonth . '-01')->subMonth()->format('Y-m');
    }

    public function nextMonth(): void
    {
        $this->currentMonth = Carbon::parse($this->currentMonth . '-01')->addMonth()->format('Y-m');
    }

    public function goToToday(): void
    {
        $this->currentMonth = Carbon::now()->format('Y-m');
    }

    protected function getViewData(): array
    {
        $date = Carbon::parse($this->currentMonth . '-01');
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        // Ambil hari Senin paling awal dari minggu pertama
        $startGrid = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);
        // Ambil hari Minggu paling akhir dari minggu penutup
        $endGrid = $endOfMonth->copy()->endOfWeek(Carbon::SUNDAY);

        // Ambil semua jadwal dalam rentang grid kalender
        $schedules = Schedule::query()
            ->where('start_time', '>=', $startGrid->copy()->startOfDay())
            ->where('start_time', '<=', $endGrid->copy()->endOfDay())
            ->orderBy('start_time', 'asc')
            ->get();

        $days = [];
        $cursor = $startGrid->copy();

        while ($cursor->lte($endGrid)) {
            $currentDateStr = $cursor->format('Y-m-d');

            // Filter kegiatan di tanggal ini
            $daySchedules = $schedules->filter(function ($item) use ($currentDateStr) {
                return Carbon::parse($item->start_time)->format('Y-m-d') === $currentDateStr;
            });

            $days[] = [
                'dayNumber' => $cursor->day,
                'isCurrentMonth' => $cursor->month === $date->month,
                'isToday' => $cursor->isToday(),
                'schedules' => $daySchedules,
            ];

            $cursor->addDay();
        }

        return [
            'monthTitle' => $date->translatedFormat('F Y'),
            'weeks' => array_chunk($days, 7),
        ];
    }
}