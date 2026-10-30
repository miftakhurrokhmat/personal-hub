<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Income;
use App\Models\Expense;
use Carbon\Carbon;

class CashflowChartWidget extends ChartWidget
{
    protected static ?string $heading = '📈 Tren Arus Kas (6 Bulan Terakhir)';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $months = [];
        $incomeData = [];
        $expenseData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthLabel = $date->translatedFormat('M Y');
            $months[] = $monthLabel;

            $incomeData[] = (float) Income::whereYear('received_at', $date->year)
                ->whereMonth('received_at', $date->month)
                ->where('payment_status', 'paid')
                ->sum('amount');

            $expenseData[] = (float) Expense::whereYear('spent_at', $date->year)
                ->whereMonth('spent_at', $date->month)
                ->sum('amount');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pemasukan Cair (Rp)',
                    'data' => $incomeData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.15)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Pengeluaran (Rp)',
                    'data' => $expenseData,
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.15)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
