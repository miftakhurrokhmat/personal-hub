<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static string $view = 'filament.pages.dashboard';

    public string $activeTab = 'agenda';

    public function mount(): void
    {
        $role = session('active_role', 'all');

        $this->activeTab = match ($role) {
            'manajer' => 'keuangan',
            default => 'agenda',
        };
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function getWidgets(): array
    {
        $role = session('active_role', 'all');

        // Jika mode Manajer: Widget kalender langsung disembunyikan
        if ($role === 'manajer') {
            return [
                \App\Filament\Widgets\FinancialHealthOverview::class,
                \App\Filament\Widgets\CashflowChartWidget::class,
                \App\Filament\Widgets\PendingInvoicesWidget::class,
            ];
        }

        // Jika mode Warga atau Developer: Widget keuangan disembunyikan total
        if (in_array($role, ['warga', 'developer'])) {
            return [
                \App\Filament\Widgets\MonthCalendarWidget::class,
                \App\Filament\Widgets\WorkLifeOverview::class,
            ];
        }

        // Jika mode Master (all): Tampilkan sesuai tombol tab yang diklik
        if ($this->activeTab === 'keuangan') {
            return [
                \App\Filament\Widgets\FinancialHealthOverview::class,
                \App\Filament\Widgets\CashflowChartWidget::class,
                \App\Filament\Widgets\PendingInvoicesWidget::class,
            ];
        }

        return [
            \App\Filament\Widgets\MonthCalendarWidget::class,
            \App\Filament\Widgets\WorkLifeOverview::class,
        ];
    }
}
