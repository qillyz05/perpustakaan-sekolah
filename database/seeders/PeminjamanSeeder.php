<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\Pengaturan;
use App\Models\User;
use Illuminate\Database\Seeder;

class PeminjamanSeeder extends Seeder
{
    public function run(): void
    {
        $siswa = User::siswa()->get();
        $petugas = User::where('role', User::ROLE_PUSTAKAWAN)->first();
        $lamaPinjam = (int) Pengaturan::get('lama_pinjam_hari', 7);

        foreach ($siswa->shuffle() as $index => $anggota) {
            $item = Buku::where('tersedia', '>', 0)->inRandomOrder()->first();

            if (! $item) {
                break;
            }

            $dikembalikan = $index % 3 !== 0;

            if ($dikembalikan) {
                $tglPinjam = now()->subDays(fake()->numberBetween(1, 30));
                $tglKembaliPlan = $tglPinjam->copy()->addDays($lamaPinjam);
                $tglKembaliAktual = $tglKembaliPlan->copy()->addDays(fake()->numberBetween(0, 4));
                $terlambat = $tglKembaliAktual->gt($tglKembaliPlan);
                $denda = $terlambat ? $tglKembaliPlan->diffInDays($tglKembaliAktual) * (int) Pengaturan::get('denda_per_hari', 500) : 0;

                Peminjaman::create([
                    'kode_pinjam' => Peminjaman::generateKodePinjam(),
                    'user_id' => $anggota->id,
                    'buku_id' => $item->id,
                    'petugas_id' => $petugas?->id,
                    'tanggal_pinjam' => $tglPinjam,
                    'tanggal_kembali' => $tglKembaliPlan,
                    'tanggal_dikembalikan' => $tglKembaliAktual,
                    'status' => Peminjaman::STATUS_DIKEMBALIKAN,
                    'denda' => $denda,
                    'status_bayar' => $denda > 0 && fake()->boolean(50) ? 'sudah_dibayar' : 'belum_dibayar',
                ]);
            } else {
                $tglPinjam = $index === 2
                    ? now()->subDays($lamaPinjam + 5)
                    : now()->subDays(fake()->numberBetween(0, 4));

                Peminjaman::create([
                    'kode_pinjam' => Peminjaman::generateKodePinjam(),
                    'user_id' => $anggota->id,
                    'buku_id' => $item->id,
                    'petugas_id' => $petugas?->id,
                    'tanggal_pinjam' => $tglPinjam,
                    'tanggal_kembali' => $tglPinjam->copy()->addDays($lamaPinjam),
                    'status' => Peminjaman::STATUS_DIPINJAM,
                ]);

                $item->decrement('tersedia');
            }
        }

        foreach ($siswa->random(min(3, $siswa->count())) as $anggota) {
            $item = Buku::where('tersedia', '>', 0)->inRandomOrder()->first();

            if (! $item) {
                break;
            }

            Peminjaman::create([
                'kode_pinjam' => Peminjaman::generateKodePinjam(),
                'user_id' => $anggota->id,
                'buku_id' => $item->id,
                'tanggal_pinjam' => now(),
                'tanggal_kembali' => now()->addDays($lamaPinjam),
                'status' => Peminjaman::STATUS_DIAJUKAN,
            ]);
        }
    }
}
