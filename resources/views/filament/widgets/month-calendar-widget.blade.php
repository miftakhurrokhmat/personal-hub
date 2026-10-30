<x-filament-widgets::widget>
    <x-filament::section>
        <style>
            .cal-wrap { width: 100%; font-family: inherit; }
            .cal-nav-bar { display: flex; justify-content: space-between; align-items: center; padding-bottom: 12px; margin-bottom: 12px; border-bottom: 1px solid #e5e7eb; }
            .cal-legend-bar { display: flex; flex-wrap: wrap; gap: 14px; margin-bottom: 12px; font-size: 12px; color: #4b5563; }
            .cal-dot-mark { display: inline-block; width: 8px; height: 8px; border-radius: 50%; margin-right: 4px; }
            .cal-grid-table { width: 100%; border-collapse: collapse; table-layout: fixed; border: 1px solid #d1d5db; background: #ffffff; }
            .cal-head-cell { background-color: #f3f4f6; color: #374151; font-size: 13px; font-weight: 700; text-align: center; padding: 8px 4px; border: 1px solid #d1d5db; width: 14.285%; }
            .cal-day-cell { height: 110px; vertical-align: top; border: 1px solid #d1d5db; padding: 6px; background-color: #ffffff; }
            .cal-day-cell:hover { background-color: #f9fafb; }
            .cal-other-month { background-color: #f9fafb; opacity: 0.45; }
            .cal-today-cell { background-color: #fffbeb !important; }
            .cal-num { font-size: 12px; font-weight: 700; color: #1f2937; display: inline-flex; align-items: center; justify-content: center; width: 22px; height: 22px; margin-bottom: 4px; }
            .cal-num-active { background-color: #f59e0b; color: #ffffff !important; border-radius: 9999px; }
            .cal-items-container { display: flex; flex-direction: column; gap: 4px; max-height: 80px; overflow-y: auto; }
            .cal-event-pill { display: block; padding: 2px 6px; font-size: 11px; line-height: 1.3; border-radius: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; cursor: pointer; text-align: left; }
            .cal-cat-work { background-color: #dbeafe; color: #1e40af; border-left: 3px solid #2563eb; }
            .cal-cat-village { background-color: #d1fae5; color: #065f46; border-left: 3px solid #059669; }
            .cal-cat-family { background-color: #fef3c7; color: #92400e; border-left: 3px solid #d97706; }
            .cal-cat-personal { background-color: #f3f4f6; color: #374151; border-left: 3px solid #6b7280; }
        </style>

        <div class="cal-wrap">
            {{-- Toolbar Navigasi Bulan --}}
            <div class="cal-nav-bar">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 20px;">🗓️</span>
                    <strong style="font-size: 17px; color: #111827;">Agenda & Kalender Kegiatan</strong>
                    <span style="background: #fef3c7; color: #b45309; padding: 3px 10px; border-radius: 6px; font-weight: 700; font-size: 13px; border: 1px solid #fde68a;">
                        {{ $monthTitle }}
                    </span>
                </div>

                <div style="display: flex; gap: 6px;">
                    <x-filament::button wire:click="prevMonth" size="xs" color="gray" icon="heroicon-m-chevron-left" />
                    <x-filament::button wire:click="goToToday" size="xs" color="gray">
                        Hari Ini
                    </x-filament::button>
                    <x-filament::button wire:click="nextMonth" size="xs" color="gray" icon="heroicon-m-chevron-right" />
                </div>
            </div>

            {{-- Legenda Kategori --}}
            <div class="cal-legend-bar">
                <span><span class="cal-dot-mark" style="background:#2563eb;"></span> 💼 Pekerjaan (Kampus / IT / Asesor)</span>
                <span><span class="cal-dot-mark" style="background:#059669;"></span> 🌾 Kegiatan Warga Desa</span>
                <span><span class="cal-dot-mark" style="background:#d97706;"></span> 👨‍👩‍👧‍👦 Agenda Keluarga</span>
                <span><span class="cal-dot-mark" style="background:#6b7280;"></span> ☕ Waktu Pribadi</span>
            </div>

            {{-- Grid Kalender --}}
            <table class="cal-grid-table">
                <thead>
                    <tr>
                        <th class="cal-head-cell">Senin</th>
                        <th class="cal-head-cell">Selasa</th>
                        <th class="cal-head-cell">Rabu</th>
                        <th class="cal-head-cell">Kamis</th>
                        <th class="cal-head-cell">Jumat</th>
                        <th class="cal-head-cell">Sabtu</th>
                        <th class="cal-head-cell" style="color: #dc2626;">Minggu</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($weeks as $weekIndex => $weekDays)
                        <tr>
                            @foreach ($weekDays as $dayIndex => $dayData)
                                @php
                                    $cellCss = 'cal-day-cell';
                                    if (! $dayData['isCurrentMonth']) {
                                        $cellCss .= ' cal-other-month';
                                    }
                                    if ($dayData['isToday']) {
                                        $cellCss .= ' cal-today-cell';
                                    }
                                @endphp
                                <td class="{{ $cellCss }}">
                                    <div class="cal-num {{ $dayData['isToday'] ? 'cal-num-active' : '' }}">
                                        {{ $dayData['dayNumber'] }}
                                    </div>

                                    <div class="cal-items-container">
                                        @foreach ($dayData['schedules'] as $item)
                                            @php
                                                $cat = $item->category ?? 'pribadi';
                                                $pillStyle = match($cat) {
                                                    'pekerjaan' => 'cal-cat-work',
                                                    'desa' => 'cal-cat-village',
                                                    'keluarga' => 'cal-cat-family',
                                                    default => 'cal-cat-personal',
                                                };
                                                $startFormatted = \Carbon\Carbon::parse($item->start_time)->format('H:i');
                                                $tip = "• Agenda: {$item->title}\n• Waktu: {$startFormatted} WIB ({$item->duration_hours} Jam)\n• Kategori: " . ucfirst($item->category);
                                                if (!empty($item->location_name)) {
                                                    $tip .= "\n• Lokasi: {$item->location_name}";
                                                }
                                            @endphp
                                            <div class="cal-event-pill {{ $pillStyle }}" title="{{ $tip }}">
                                                <strong>{{ $startFormatted }}</strong> {{ $item->title }}
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>