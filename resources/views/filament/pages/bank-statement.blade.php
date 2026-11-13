<x-filament-panels::page>
    {{-- Form Filter Rekening & Periode --}}
    {{ $this->form }}

    @php
    $statement = $this->getStatementData();
    @endphp

    @if (! $statement)
    <div class="rounded-xl border border-gray-200 bg-white p-12 text-center shadow-sm dark:border-white/10 dark:bg-gray-900">
        <x-heroicon-o-building-library class="mx-auto h-12 w-12 text-gray-400" />
        <h3 class="mt-3 text-base font-semibold text-gray-950 dark:text-white">
            Pilih Rekening & Periode
        </h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Silakan pilih <strong>Rekening / Bank</strong> dan <strong>Periode (Tahun - Bulan)</strong> di atas untuk menampilkan rincian mutasi dan saldo akhir.
        </p>
    </div>
    @else
    {{-- Ringkasan Kartu Atas --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Saldo Awal</div>
            <div class="mt-1 text-lg font-bold text-gray-900 dark:text-white">
                IDR {{ number_format($statement['opening_balance'], 2, ',', '.') }}
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Mutasi Masuk (Kredit)</div>
            <div class="mt-1 text-lg font-bold text-success-600 dark:text-success-400">
                + IDR {{ number_format($statement['total_credit'], 2, ',', '.') }}
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Mutasi Keluar (Debet)</div>
            <div class="mt-1 text-lg font-bold text-danger-600 dark:text-danger-400">
                - IDR {{ number_format($statement['total_debit'], 2, ',', '.') }}
            </div>
        </div>

        <div class="rounded-xl border border-primary-300 bg-primary-50/50 p-4 shadow-sm dark:border-primary-500/30 dark:bg-primary-950/20">
            <div class="text-xs font-medium text-primary-700 dark:text-primary-400">Saldo Akhir ({{ $statement['period_label'] }})</div>
            <div class="mt-1 text-lg font-bold text-primary-700 dark:text-primary-300">
                IDR {{ number_format($statement['closing_balance'], 2, ',', '.') }}
            </div>
        </div>
    </div>

    {{-- Tabel Mutasi Rekening --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-900">
        <div class="border-b border-gray-200 px-6 py-4 dark:border-white/10">
            <h3 class="text-base font-semibold text-gray-950 dark:text-white">
                Rekening Koran: {{ $statement['account']->name }}
                @if ($statement['account']->account_number)
                <span class="text-sm font-normal text-gray-500">({{ $statement['account']->account_number }})</span>
                @endif
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400">
                Periode: {{ $statement['period_label'] }}
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-gray-200 text-left text-sm dark:divide-white/10">
                <thead class="bg-gray-50 dark:bg-white/5">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-200">Tanggal</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-200">Keterangan Transaksi</th>
                        <th class="px-4 py-3 font-semibold text-gray-700 dark:text-gray-200">Kategori</th>
                        <th class="px-4 py-3 text-right font-semibold text-success-600 dark:text-success-400">Masuk (Kredit)</th>
                        <th class="px-4 py-3 text-right font-semibold text-danger-600 dark:text-danger-400">Keluar (Debet)</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700 dark:text-gray-200">Saldo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                    {{-- Baris 1: Saldo Awal --}}
                    <tr class="bg-gray-50/70 font-medium dark:bg-white/5">
                        <td class="whitespace-nowrap px-4 py-3 text-gray-600 dark:text-gray-300">
                            {{ $statement['opening_date'] }}
                        </td>
                        <td class="px-4 py-3 text-gray-900 dark:text-white" colspan="2">
                            {{ $statement['opening_note'] }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right text-gray-400">-</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right text-gray-400">-</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right font-bold text-gray-900 dark:text-white">
                            IDR {{ number_format($statement['opening_balance'], 2, ',', '.') }}
                        </td>
                    </tr>

                    {{-- Baris Mutasi Masuk & Keluar --}}
                    @forelse ($statement['rows'] as $row)
                    <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                        <td class="whitespace-nowrap px-4 py-3 text-gray-600 dark:text-gray-300">
                            {{ \Carbon\Carbon::parse($row['date'])->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3 text-gray-900 dark:text-white">
                            {{ $row['description'] }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3">
                            <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700 dark:bg-white/10 dark:text-gray-300">
                                {{ $row['category'] }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right font-medium text-success-600 dark:text-success-400">
                            {{ $row['credit'] > 0 ? 'IDR ' . number_format($row['credit'], 2, ',', '.') : '-' }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right font-medium text-danger-600 dark:text-danger-400">
                            {{ $row['debit'] > 0 ? 'IDR ' . number_format($row['debit'], 2, ',', '.') : '-' }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right font-semibold text-gray-900 dark:text-white">
                            IDR {{ number_format($row['balance'], 2, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                            Tidak ada mutasi pemasukan maupun pengeluaran pada periode ini.
                        </td>
                    </tr>
                    @endforelse

                    {{-- Baris Terakhir: Saldo Akhir --}}
                    <tr class="bg-primary-50/70 font-bold dark:bg-primary-950/30">
                        <td class="whitespace-nowrap px-4 py-3.5 text-primary-900 dark:text-primary-200">
                            {{ $statement['closing_date'] }}
                        </td>
                        <td class="px-4 py-3.5 text-primary-900 dark:text-primary-200" colspan="2">
                            SALDO AKHIR PERIODE
                        </td>
                        <td class="whitespace-nowrap px-4 py-3.5 text-right text-success-700 dark:text-success-400">
                            IDR {{ number_format($statement['total_credit'], 2, ',', '.') }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3.5 text-right text-danger-700 dark:text-danger-400">
                            IDR {{ number_format($statement['total_debit'], 2, ',', '.') }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3.5 text-right text-base text-primary-700 dark:text-primary-300">
                            IDR {{ number_format($statement['closing_balance'], 2, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endif
</x-filament-panels::page>