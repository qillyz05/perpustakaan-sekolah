<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Bu Siti Aminah',
            'email' => 'pustakawan@sekolah.sch.id',
            'password' => Hash::make('password'),
            'role' => User::ROLE_PUSTAKAWAN,
            'telepon' => '081234567890',
        ]);

        $siswa = [
            ['name' => 'Ahmad Fauzi', 'nis' => '2024001', 'kelas' => 'XII IPA 1', 'telepon' => '081211111111'],
            ['name' => 'Siti Rahma', 'nis' => '2024002', 'kelas' => 'XII IPA 2', 'telepon' => '081211111112'],
            ['name' => 'Budi Santoso', 'nis' => '2024003', 'kelas' => 'XI IPS 1', 'telepon' => '081211111113'],
            ['name' => 'Dewi Lestari', 'nis' => '2024004', 'kelas' => 'XI IPS 2', 'telepon' => '081211111114'],
            ['name' => 'Rizky Pratama', 'nis' => '2024005', 'kelas' => 'X Bahasa', 'telepon' => '081211111115'],
            ['name' => 'Nadia Kusuma', 'nis' => '2024006', 'kelas' => 'X IPA 1', 'telepon' => '081211111116'],
            ['name' => 'Fajar Nugroho', 'nis' => '2024007', 'kelas' => 'X IPA 2', 'telepon' => '081211111117'],
            ['name' => 'Ayu Wulandari', 'nis' => '2024008', 'kelas' => 'XI IPA 1', 'telepon' => '081211111118'],
        ];

        foreach ($siswa as $data) {
            User::create($data + [
                'email' => $data['nis'].'@siswa.sekolah.sch.id',
                'password' => Hash::make('password'),
                'role' => User::ROLE_SISWA,
            ]);
        }
    }
}
