<x-filament-panels::page>
    @php
        $role = session('active_role', 'all');
    @endphp

    {{-- Navigasi Tab: Hanya tampil jika mode 'all' atau peran yang relevan --}}
    @if ($role === 'all')
        <div style="display: flex; gap: 8px; border-bottom: 1px solid #e5e7eb; padding-bottom: 12px; margin-bottom: 16px;">
            <button
                type="button"
                wire:click="setTab('agenda')"
                style="display: flex; align-items: center; gap: 8px; padding: 8px 16px; font-size: 14px; font-weight: 600; border-radius: 8px; border: none; cursor: pointer; transition: all 0.2s; {{ $activeTab === 'agenda' ? 'background-color: #f59e0b; color: #ffffff;' : 'background-color: #f3f4f6; color: #374151;' }}"
            >
                <span>🗓️</span>
                <span>Ritme Hidup & Kalender</span>
            </button>

            <button
                type="button"
                wire:click="setTab('keuangan')"
                style="display: flex; align-items: center; gap: 8px; padding: 8px 16px; font-size: 14px; font-weight: 600; border-radius: 8px; border: none; cursor: pointer; transition: all 0.2s; {{ $activeTab === 'keuangan' ? 'background-color: #f59e0b; color: #ffffff;' : 'background-color: #f3f4f6; color: #374151;' }}"
            >
                <span>💰</span>
                <span>Finansial & Arus Kas</span>
            </button>
        </div>
    @endif

    {{-- Render Widget Sesuai Role & Tab Aktif --}}
    <x-filament-widgets::widgets
        :widgets="$this->getWidgets()"
        :columns="$this->getColumns()"
    />
</x-filament-panels::page>