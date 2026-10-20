<?php

namespace Database\Seeders;

use App\Models\Pengaturan;
use Illuminate\Database\Seeder;

class PengaturanSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'nama_perpustakaan' => 'Perpustakaan Sekolah',
            'lama_pinjam_hari' => '7',
            'batas_pinjam_maks' => '3',
            'denda_per_hari' => '500',
        ];

        foreach ($settings as $key => $value) {
            Pengaturan::set($key, $value);
        }
    }
}
