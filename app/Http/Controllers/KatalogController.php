<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\KategoriBuku;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KatalogController extends Controller
{
    public function home(): View
    {
        return view('public.home', [
            'totalBuku' => Buku::count(),
            'totalEksemplar' => Buku::sum('jumlah_eksemplar'),
            'totalAnggota' => User::siswa()->count(),
            'totalKategori' => KategoriBuku::count(),
            'unggulan' => Buku::with('kategori')->latest()->take(8)->get(),
            'terpopuler' => Buku::with('kategori')->withCount('peminjaman')->orderByDesc('peminjaman_count')->take(4)->get(),
            'kategori' => KategoriBuku::withCount('buku')->get(),
        ]);
    }

    public function index(Request $request): View
    {
        $buku = Buku::query()
            ->with('kategori')
            ->pencarian($request->string('q')->trim()->value() ?: null)
            ->kategoriSlug($request->string('kategori')->value() ?: null)
            ->when($request->string('tersedia')->value() === '1', fn ($q) => $q->where('tersedia', '>', 0))
            ->when($request->string('sort')->value() === 'terlama', fn ($q) => $q->oldest())
            ->when($request->string('sort')->value() === 'judul', fn ($q) => $q->orderBy('judul'))
            ->when(! in_array($request->string('sort')->value(), ['terlama', 'judul'], true), fn ($q) => $q->latest())
            ->paginate(12)
            ->withQueryString();

        return view('public.katalog', [
            'buku' => $buku,
            'kategori' => KategoriBuku::withCount('buku')->get(),
        ]);
    }

    public function show(Buku $buku): View
    {
        $buku->load('kategori');

        return view('public.detail', [
            'buku' => $buku,
            'terkait' => Buku::with('kategori')
                ->where('kategori_id', $buku->kategori_id)
                ->whereKeyNot($buku->id)
                ->take(4)
                ->get(),
            'sedangDipinjam' => Peminjaman::query()
                ->where('buku_id', $buku->id)
                ->where('status', Peminjaman::STATUS_DIPINJAM)
                ->count(),
        ]);
    }
}
