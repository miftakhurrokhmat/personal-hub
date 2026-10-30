<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $jsonPath = database_path('seeders/indonesia_regions.json');

        if (! File::exists($jsonPath)) {
            $this->command->error("File dataset [{$jsonPath}] tidak ditemukan!");
            return;
        }

        $regions = json_decode(File::get($jsonPath), true);

        $this->command->info('Memulai seeding 38 Provinsi & 514 Kabupaten/Kota se-Indonesia...');

        DB::transaction(function () use ($regions) {
            $now = now();

            foreach ($regions as $provinceName => $cities) {
                // 1. Insert Provinsi
                $provinceId = DB::table('provinces')->insertGetId([
                    'name' => $provinceName,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                // 2. Siapkan batch insert Kota/Kabupaten untuk efisiensi query
                $cityData = [];
                foreach ($cities as $city) {
                    $cityData[] = [
                        'province_id' => $provinceId,
                        'name' => $city[0],
                        'type' => $city[1], // 'kabupaten' atau 'kota'
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                // Batch insert per provinsi
                DB::table('cities')->insert($cityData);
            }
        });

        $this->command->info('Seeding seluruh wilayah Indonesia berhasil diselesaikan!');
    }
}
