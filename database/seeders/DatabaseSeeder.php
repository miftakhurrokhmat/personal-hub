<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // 1. Jalankan Seeder Master 38 Provinsi & 514 Kota/Kabupaten
        $this->call([
            RegionSeeder::class,
        ]);

        // 2. Akun Test (opsional, bisa dibiarkan atau dikomentari jika sudah ada filament user)
        // User::factory()->create([
        //     'name' => 'Admin Test',
        //     'email' => 'admin@example.com',
        // ]);
    }
}
