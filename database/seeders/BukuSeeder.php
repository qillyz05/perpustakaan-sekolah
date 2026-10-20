<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\KategoriBuku;
use Illuminate\Database\Seeder;

class BukuSeeder extends Seeder
{
    public function run(): void
    {
        $buku = [
            'Fiksi' => [
                ['Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', 2005],
                ['Bumi Manusia', 'Pramoedya Ananta Toer', 'Hasta Mitra', 1980],
                ['Tenggelamnya Kapal Van der Wijck', 'Hamka', 'Balai Pustaka', 1939],
                ['Ayat-Ayat Cinta', 'Habiburrahman El Shirazy', 'Republika', 2004],
                ['Perahu Kertas', 'Dee Lestari', 'Bentang Pustaka', 2009],
                ['Negeri 5 Menara', 'Ahmad Fuadi', 'Republika', 2009],
            ],
            'Sains & Teknologi' => [
                ['Fisika untuk SMA/MA Kelas X', 'Erlina Yuliati', 'Erlangga', 2021],
                ['Biologi Kelas XI', 'Murti Wijayati', 'Erlangga', 2021],
                ['Matematika untuk SMA/MA Kelas XI', 'Tim Penulis Yudhistira', 'Yudhistira', 2021],
                ['Pemrograman Web dengan HTML & CSS', 'Jubilee Enterprise', 'Elex Media Komputindo', 2018],
                ['Pengantar Ilmu Komputer', 'Thomas Cormen', 'Informatika', 2017],
                ['Kosmos: Perjalanan di Semesta Kita', 'Carl Sagan', 'Republika', 2017],
            ],
            'Sejarah' => [
                ['Sejarah Indonesia Modern', 'M.C. Ricklefs', 'Kanisius', 2008],
                ['Gajah Mada: Kuasa dan Cita-Cita', 'Gorong Shiraishi', 'Komunitas Bambu', 2018],
                ['Indonesia: From Sabang to Merauke', 'P.J. Drooglever', 'KPG', 2009],
                ['Perang Diponegoro 1825-1830', 'Peter Carey', 'Omah Laku', 2017],
            ],
            'Sastra' => [
                ['Sitti Nurbaya', 'Marah Rusli', 'Balai Pustaka', 1922],
                ['Roro Mendut', 'Agus Salim', 'Balai Pustaka', 1952],
                ['Poetra: Kumpulan Puisi Chairil Anwar', 'Chairil Anwar', 'Gramedia', 2015],
                ['Bila Malam Berganti Hari', 'Pramoedya Ananta Toer', 'Hasta Mitra', 1988],
            ],
            'Agama' => [
                ['Sebuah Tuntunan Hidup Islam', 'K.H. Hasyim Asy\'ari', "Lajnah Ta'lif wan Nasyr", 2010],
                ['Mengenal Akhlak Terpuji', 'Abdurrahman An-Nahlawi', 'Gema Insani', 2012],
                ["Tafsir Ringkas Al-Qur'an Per Juz", 'Kemenag RI', 'Republika', 2019],
            ],
            'Ensiklopedia & Referensi' => [
                ['Ensiklopedia Sains untuk Pelajar', 'Tim Ensiklopedia Dunia', 'Lingua Kreatif', 2020],
                ['Kamus Besar Bahasa Indonesia Edisi Kelima', 'Pusat Pengembangan dan Pembinaan Bahasa', 'Balai Pustaka', 2016],
                ['Atlas Lengkap Pelajaran IPS', 'Tim Grafatama Nusantara', 'Grafatama', 2019],
                ['Ensiklopedia Hewan Dunia', 'Tim Anak Cerdas', 'Lingua Kreatif', 2018],
            ],
        ];

        foreach ($buku as $kategoriNama => $daftar) {
            $kategori = KategoriBuku::where('nama', $kategoriNama)->firstOrFail();

            foreach ($daftar as [$judul, $penulis, $penerbit, $tahun]) {
                $jumlah = fake()->numberBetween(2, 6);

                Buku::create([
                    'kode_buku' => Buku::generateKodeBuku(),
                    'judul' => $judul,
                    'penulis' => $penulis,
                    'penerbit' => $penerbit,
                    'tahun_terbit' => $tahun,
                    'isbn' => fake()->unique()->numerify('978-602-####-###-#'),
                    'kategori_id' => $kategori->id,
                    'sinopsis' => "Buku {$judul} karya {$penulis}. Koleksi perpustakaan sekolah yang dapat dipinjam oleh siswa.",
                    'rak' => 'Rak '.$kategori->nama[0].fake()->numberBetween(1, 5),
                    'jumlah_eksemplar' => $jumlah,
                    'tersedia' => $jumlah,
                ]);
            }
        }
    }
}
