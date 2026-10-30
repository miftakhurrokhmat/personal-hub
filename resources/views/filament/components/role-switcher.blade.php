<div x-data class="relative flex items-center pr-3">
    @php
    $currentRole = session('active_role', 'all');
    $roles = [
    'all' => ['label' => 'Semua Fitur (Master)', 'icon' => '👑'],
    'warga' => ['label' => 'Warga / Individu', 'icon' => '🌾'],
    'developer' => ['label' => 'Pengembang & Teknis', 'icon' => '💻'],
    'manajer' => ['label' => 'Manajer & Bisnis', 'icon' => '👔'],
    ];
    @endphp

    <x-filament::dropdown placement="bottom-end">
        <x-slot name="trigger">
            <button
                type="button"
                class="flex items-center gap-2 px-3 py-1.5 text-xs font-semibold rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 border border-gray-300 dark:border-gray-600 transition">
                <span>{{ $roles[$currentRole]['icon'] }}</span>
                <span>Mode: <strong>{{ $roles[$currentRole]['label'] }}</strong></span>
                <x-heroicon-m-chevron-down class="w-3.5 h-3.5 opacity-60" />
            </button>
        </x-slot>

        <x-filament::dropdown.list>
            @foreach ($roles as $key => $val)
            <x-filament::dropdown.list.item
                :href="route('switch.role', ['role' => $key])"
                tag="a"
                :icon="$key === $currentRole ? 'heroicon-m-check' : null"
                color="{{ $key === $currentRole ? 'primary' : 'gray' }}">
                <span class="mr-1.5">{{ $val['icon'] }}</span> {{ $val['label'] }}
            </x-filament::dropdown.list.item>
            @endforeach
        </x-filament::dropdown.list>
    </x-filament::dropdown>
</div>