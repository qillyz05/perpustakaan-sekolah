<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today();

        $statistik = [
            'total_buku' => Buku::count(),
            'total_eksemplar' => Buku::sum('jumlah_eksemplar'),
            'total_anggota' => User::siswa()->count(),
            'sedang_dipinjam' => Peminjaman::where('status', Peminjaman::STATUS_DIPINJAM)->count(),
            'terlambat' => Peminjaman::terlambat()->count(),
            'denda_belum_bayar' => Peminjaman::where('status_bayar', 'belum_dibayar')->sum('denda'),
        ];

        $grafik = Peminjaman::query()
            ->selectRaw('DATE(tanggal_pinjam) as tanggal, COUNT(*) as jumlah')
            ->where('tanggal_pinjam', '>=', $today->copy()->subDays(13))
            ->groupBy('tanggal')
            ->pluck('jumlah', 'tanggal');

        $chart = collect(range(13, 0))->map(function (int $i) use ($today, $grafik) {
            $date = $today->copy()->subDays($i);

            return [
                'label' => $date->translatedFormat('d M'),
                'value' => (int) ($grafik[$date->toDateString()] ?? 0),
            ];
        });

        $terbaru = Peminjaman::with(['siswa', 'buku'])
            ->latest()
            ->take(6)
            ->get();

        $bukuPopuler = Buku::withCount('peminjaman')
            ->orderByDesc('peminjaman_count')
            ->take(5)
            ->get();

        return view('admin.dashboard', [
            'statistik' => $statistik,
            'chart' => $chart,
            'terbaru' => $terbaru,
            'bukuPopuler' => $bukuPopuler,
        ]);
    }
}
