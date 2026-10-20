<?php

namespace Database\Seeders;

use App\Models\KategoriBuku;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = [
            ['nama' => 'Fiksi', 'deskripsi' => 'Novel, cerpen, dan karya sastra imajinatif.'],
            ['nama' => 'Sains & Teknologi', 'deskripsi' => 'Buku sains, teknologi, dan komputer.'],
            ['nama' => 'Sejarah', 'deskripsi' => 'Sejarah Indonesia dan dunia.'],
            ['nama' => 'Sastra', 'deskripsi' => 'Puisi, drama, dan karya sastra klasik.'],
            ['nama' => 'Agama', 'deskripsi' => 'Buku keagamaan dan akhlak.'],
            ['nama' => 'Ensiklopedia & Referensi', 'deskripsi' => 'Kamus, ensiklopedia, dan referensi pelajaran.'],
        ];

        foreach ($kategori as $item) {
            KategoriBuku::create($item);
        }
    }
}
