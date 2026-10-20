<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PeminjamanRequest;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Models\Pengaturan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PeminjamanController extends Controller
{
    public function index(Request $request): View
    {
        $peminjaman = Peminjaman::query()
            ->with(['siswa', 'buku'])
            ->when($request->string('status')->value() === 'diajukan', fn ($q) => $q->where('status', Peminjaman::STATUS_DIAJUKAN))
            ->when($request->string('status')->value() === 'dipinjam', fn ($q) => $q->where('status', Peminjaman::STATUS_DIPINJAM))
            ->when($request->string('status')->value() === 'dikembalikan', fn ($q) => $q->where('status', Peminjaman::STATUS_DIKEMBALIKAN))
            ->when($request->string('status')->value() === 'terlambat', fn ($q) => $q->terlambat())
            ->when($request->string('status')->value() === 'denda', fn ($q) => $q->where('denda', '>', 0)->where('status_bayar', 'belum_dibayar'))
            ->when($request->filled('q'), function ($q) use ($request) {
                $kata = $request->string('q')->trim()->value();
                $q->where(function ($query) use ($kata) {
                    $query->where('kode_pinjam', 'like', "%{$kata}%")
                        ->orWhereHas('siswa', fn ($s) => $s->where('name', 'like', "%{$kata}%")->orWhere('nis', 'like', "%{$kata}%"))
                        ->orWhereHas('buku', fn ($b) => $b->where('judul', 'like', "%{$kata}%"));
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.peminjaman.index', [
            'peminjaman' => $peminjaman,
            'ringkasan' => [
                'diajukan' => Peminjaman::where('status', Peminjaman::STATUS_DIAJUKAN)->count(),
                'dipinjam' => Peminjaman::where('status', Peminjaman::STATUS_DIPINJAM)->count(),
                'terlambat' => Peminjaman::terlambat()->count(),
                'denda' => Peminjaman::where('status_bayar', 'belum_dibayar')->sum('denda'),
            ],
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.peminjaman.create', [
            'siswa' => User::siswa()->orderBy('name')->get(),
            'buku' => Buku::with('kategori')->where('tersedia', '>', 0)->orderBy('judul')->get(),
            'lamaPinjam' => (int) Pengaturan::get('lama_pinjam_hari', 7),
            'batasPinjam' => (int) Pengaturan::get('batas_pinjam_maks', 3),
            'pilihBuku' => $request->integer('buku_id') ?: null,
        ]);
    }

    public function store(PeminjamanRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $siswa = User::findOrFail($data['user_id']);
        $buku = Buku::findOrFail($data['buku_id']);

        $aktif = $siswa->peminjaman()->aktif()->count();
        $batas = (int) Pengaturan::get('batas_pinjam_maks', 3);

        if ($aktif >= $batas) {
            return back()->withInput()->with('error', "Siswa sudah memiliki {$aktif} pinjaman aktif (batas maksimal {$batas}).");
        }

        if (! $buku->isTersedia()) {
            return back()->withInput()->with('error', 'Stok buku ini sedang tidak tersedia.');
        }

        if ($siswa->peminjaman()->where('buku_id', $buku->id)->aktif()->exists()) {
            return back()->withInput()->with('error', 'Siswa masih meminjam atau mengajukan buku yang sama.');
        }

        DB::transaction(function () use ($data, $request, $buku) {
            Peminjaman::create([
                'kode_pinjam' => Peminjaman::generateKodePinjam(),
                'user_id' => $data['user_id'],
                'buku_id' => $data['buku_id'],
                'petugas_id' => $request->user()->id,
                'tanggal_pinjam' => $data['tanggal_pinjam'],
                'tanggal_kembali' => $data['tanggal_kembali'],
                'status' => Peminjaman::STATUS_DIPINJAM,
                'catatan' => $data['catatan'] ?? null,
            ]);

            $buku->decrement('tersedia');
        });

        return redirect()->route('admin.peminjaman.index')->with('success', 'Peminjaman berhasil dicatat.');
    }

    public function show(Peminjaman $peminjaman): View
    {
        $peminjaman->load(['siswa', 'buku.kategori', 'petugas']);

        return view('admin.peminjaman.show', [
            'peminjaman' => $peminjaman,
            'dendaSaatIni' => $peminjaman->isDipinjam() ? $peminjaman->hitungDenda() : $peminjaman->denda,
        ]);
    }

    public function setujui(Request $request, Peminjaman $peminjaman): RedirectResponse
    {
        if (! $peminjaman->isDiajukan()) {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $buku = $peminjaman->buku;

        if (! $buku->isTersedia()) {
            return back()->with('error', 'Stok buku tidak tersedia. Pengajuan tidak dapat disetujui.');
        }

        $batas = (int) Pengaturan::get('batas_pinjam_maks', 3);
        $aktifLain = $peminjaman->siswa->peminjaman()
            ->where('id', '!=', $peminjaman->id)
            ->where('status', Peminjaman::STATUS_DIPINJAM)
            ->count();

        if ($aktifLain >= $batas) {
            return back()->with('error', "Siswa sudah mencapai batas {$batas} pinjaman aktif.");
        }

        $lamaPinjam = (int) Pengaturan::get('lama_pinjam_hari', 7);

        DB::transaction(function () use ($request, $peminjaman, $buku, $lamaPinjam) {
            $peminjaman->update([
                'status' => Peminjaman::STATUS_DIPINJAM,
                'petugas_id' => $request->user()->id,
                'tanggal_pinjam' => Carbon::today(),
                'tanggal_kembali' => Carbon::today()->addDays($lamaPinjam),
            ]);

            $buku->decrement('tersedia');
        });

        return back()->with('success', 'Pengajuan disetujui. Buku siap diserahkan kepada siswa.');
    }

    public function tolak(Request $request, Peminjaman $peminjaman): RedirectResponse
    {
        if (! $peminjaman->isDiajukan()) {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $alasan = $request->string('alasan')->trim()->value();

        $peminjaman->update([
            'status' => Peminjaman::STATUS_DIKEMBALIKAN,
            'petugas_id' => $request->user()->id,
            'denda' => 0,
            'status_bayar' => 'sudah_dibayar',
            'catatan' => $alasan ?: $peminjaman->catatan,
        ]);

        return back()->with('success', 'Pengajuan peminjaman ditolak.');
    }

    public function destroy(Peminjaman $peminjaman): RedirectResponse
    {
        DB::transaction(function () use ($peminjaman) {
            if ($peminjaman->isDipinjam()) {
                $peminjaman->buku()->increment('tersedia');
            }
            $peminjaman->delete();
        });

        return redirect()->route('admin.peminjaman.index')->with('success', 'Data peminjaman berhasil dihapus.');
    }

    public function kembalikan(Request $request, Peminjaman $peminjaman): RedirectResponse
    {
        if (! $peminjaman->isDipinjam()) {
            return back()->with('error', 'Peminjaman ini sudah dikembalikan sebelumnya.');
        }

        $kembali = $request->filled('tanggal_dikembalikan')
            ? Carbon::parse($request->input('tanggal_dikembalikan'))
            : Carbon::today();

        DB::transaction(function () use ($peminjaman, $kembali) {
            $peminjaman->tanggal_dikembalikan = $kembali;
            $peminjaman->status = Peminjaman::STATUS_DIKEMBALIKAN;
            $peminjaman->denda = $peminjaman->hitungDenda();
            $peminjaman->status_bayar = $peminjaman->denda > 0 ? 'belum_dibayar' : 'sudah_dibayar';
            $peminjaman->save();

            $peminjaman->buku()->increment('tersedia');
        });

        $pesan = 'Buku berhasil dikembalikan.';
        if ($peminjaman->denda > 0) {
            $pesan .= ' Denda: Rp '.number_format((float) $peminjaman->denda, 0, ',', '.').'.';
        }

        return redirect()->route('admin.peminjaman.index')->with('success', $pesan);
    }

    public function bayarDenda(Peminjaman $peminjaman): RedirectResponse
    {
        if ($peminjaman->denda <= 0) {
            return back()->with('error', 'Tidak ada denda pada peminjaman ini.');
        }

        $peminjaman->update(['status_bayar' => 'sudah_dibayar']);

        return back()->with('success', 'Denda berhasil ditandai sebagai lunas.');
    }

    public function perpanjang(Peminjaman $peminjaman): RedirectResponse
    {
        if (! $peminjaman->isDipinjam()) {
            return back()->with('error', 'Hanya peminjaman aktif yang dapat diperpanjang.');
        }

        $lama = (int) Pengaturan::get('lama_pinjam_hari', 7);
        $peminjaman->update(['tanggal_kembali' => $peminjaman->tanggal_kembali->copy()->addDays($lama)]);

        return back()->with('success', "Tenggat perpanjang {$lama} hari menjadi {$peminjaman->tanggal_kembali->translatedFormat('d M Y')}.");
    }
}
