<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\Pengaturan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AreaSiswaController extends Controller
{
    public function dashboard(Request $request): View
    {
        $siswa = $request->user();

        $aktif = $siswa->peminjaman()
            ->with('buku.kategori')
            ->where('status', Peminjaman::STATUS_DIPINJAM)
            ->orderBy('tanggal_kembali')
            ->get();

        $pengajuan = $siswa->peminjaman()
            ->with('buku.kategori')
            ->where('status', Peminjaman::STATUS_DIAJUKAN)
            ->latest()
            ->get();

        $batas = (int) Pengaturan::get('batas_pinjam_maks', 3);
        $jumlahAktif = $aktif->count() + $pengajuan->count();

        return view('siswa.dashboard', [
            'aktif' => $aktif,
            'pengajuan' => $pengajuan,
            'statistik' => [
                'aktif' => $aktif->count(),
                'pengajuan' => $pengajuan->count(),
                'terlambat' => $aktif->filter(fn ($p) => $p->tanggal_kembali->isPast())->count(),
                'total' => $siswa->peminjaman()->count(),
                'denda' => $siswa->peminjaman()->where('status_bayar', 'belum_dibayar')->sum('denda'),
            ],
            'batas' => $batas,
            'sisaKuota' => max(0, $batas - $jumlahAktif),
        ]);
    }

    public function ajukan(Request $request, Buku $buku): RedirectResponse
    {
        $siswa = $request->user();
        $batas = (int) Pengaturan::get('batas_pinjam_maks', 3);
        $lamaPinjam = (int) Pengaturan::get('lama_pinjam_hari', 7);

        if (! $buku->isTersedia()) {
            return back()->with('error', 'Stok buku ini sedang tidak tersedia.');
        }

        $aktif = $siswa->peminjaman()->aktif()->count();
        if ($aktif >= $batas) {
            return back()->with('error', "Batas maksimal {$batas} peminjaman aktif sudah tercapai. Kembalikan buku terlebih dahulu.");
        }

        if ($siswa->peminjaman()->where('buku_id', $buku->id)->aktif()->exists()) {
            return back()->with('error', 'Anda sudah memiliki pengajuan atau pinjaman aktif untuk buku ini.');
        }

        Peminjaman::create([
            'kode_pinjam' => Peminjaman::generateKodePinjam(),
            'user_id' => $siswa->id,
            'buku_id' => $buku->id,
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_kembali' => now()->addDays($lamaPinjam)->toDateString(),
            'status' => Peminjaman::STATUS_DIAJUKAN,
            'catatan' => $request->input('catatan'),
        ]);

        return redirect()->route('siswa.dashboard')
            ->with('success', 'Pengajuan peminjaman berhasil dikirim. Menunggu persetujuan pustakawan.');
    }

    public function batalkan(Request $request, Peminjaman $peminjaman): RedirectResponse
    {
        abort_unless($peminjaman->user_id === $request->user()->id, 403);

        if (! $peminjaman->isDiajukan()) {
            return back()->with('error', 'Hanya pengajuan yang belum disetujui yang dapat dibatalkan.');
        }

        $peminjaman->delete();

        return redirect()->route('siswa.dashboard')->with('success', 'Pengajuan peminjaman dibatalkan.');
    }

    public function riwayat(Request $request): View
    {
        $riwayat = $request->user()->peminjaman()
            ->with('buku.kategori')
            ->latest()
            ->paginate(10);

        return view('siswa.riwayat', ['riwayat' => $riwayat]);
    }
}
