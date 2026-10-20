<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Buku;
use App\Models\Peminjaman;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    public function index(Request $request): View
    {
        [$mulai, $sampai] = $this->periode($request);

        return view('admin.laporan.index', array_merge(
            ['mulai' => $mulai, 'sampai' => $sampai],
            $this->data($mulai, $sampai)
        ));
    }

    public function cetak(Request $request): View
    {
        [$mulai, $sampai] = $this->periode($request);

        return view('admin.laporan.cetak', array_merge(
            ['mulai' => $mulai, 'sampai' => $sampai],
            $this->data($mulai, $sampai)
        ));
    }

    public function export(Request $request): StreamedResponse
    {
        [$mulai, $sampai] = $this->periode($request);
        $data = $this->data($mulai, $sampai);

        $nama = 'laporan-peminjaman-'.$mulai->format('Ymd').'-'.$sampai->format('Ymd').'.csv';

        return response()->streamDownload(function () use ($data) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Kode Pinjam', 'Siswa', 'NIS', 'Buku', 'Tanggal Pinjam', 'Tenggat', 'Dikembalikan', 'Status', 'Denda', 'Bayar']);

            foreach ($data['peminjaman'] as $item) {
                fputcsv($out, [
                    $item->kode_pinjam,
                    $item->siswa->name,
                    $item->siswa->nis,
                    $item->buku->judul,
                    $item->tanggal_pinjam->format('d/m/Y'),
                    $item->tanggal_kembali->format('d/m/Y'),
                    $item->tanggal_dikembalikan?->format('d/m/Y') ?? '-',
                    $item->isDipinjam() && $item->hariTerlambat() > 0 ? 'Terlambat' : ucfirst($item->status),
                    $item->denda,
                    $item->status_bayar === 'sudah_dibayar' ? 'Lunas' : 'Belum Bayar',
                ]);
            }

            fclose($out);
        }, $nama, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function periode(Request $request): array
    {
        $mulai = $request->filled('mulai')
            ? Carbon::parse($request->input('mulai'))->startOfDay()
            : Carbon::today()->startOfMonth();
        $sampai = $request->filled('sampai')
            ? Carbon::parse($request->input('sampai'))->endOfDay()
            : Carbon::today()->endOfDay();

        return [$mulai, $sampai];
    }

    private function data(Carbon $mulai, Carbon $sampai): array
    {
        $peminjaman = Peminjaman::with(['siswa', 'buku'])
            ->whereBetween('tanggal_pinjam', [$mulai, $sampai])
            ->latest()
            ->get();

        return [
            'peminjaman' => $peminjaman,
            'ringkasan' => [
                'total' => $peminjaman->count(),
                'dipinjam' => $peminjaman->where('status', Peminjaman::STATUS_DIPINJAM)->count(),
                'dikembalikan' => $peminjaman->where('status', Peminjaman::STATUS_DIKEMBALIKAN)->count(),
                'terlambat' => $peminjaman->filter(fn ($p) => $p->hariTerlambat() > 0)->count(),
                'total_denda' => $peminjaman->sum('denda'),
                'denda_belum' => $peminjaman->where('status_bayar', 'belum_dibayar')->sum('denda'),
            ],
            'populer' => Buku::withCount(['peminjaman' => fn ($q) => $q->whereBetween('tanggal_pinjam', [$mulai, $sampai])])
                ->orderByDesc('peminjaman_count')
                ->take(5)
                ->get(),
        ];
    }
}
