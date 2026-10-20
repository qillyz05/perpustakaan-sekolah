<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BukuRequest;
use App\Models\Buku;
use App\Models\KategoriBuku;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BukuController extends Controller
{
    public function index(Request $request): View
    {
        $buku = Buku::query()
            ->with('kategori')
            ->pencarian($request->string('q')->trim()->value() ?: null)
            ->kategoriSlug($request->string('kategori')->value() ?: null)
            ->when($request->string('stok')->value() === 'habis', fn ($q) => $q->where('tersedia', 0))
            ->when($request->string('stok')->value() === 'tersedia', fn ($q) => $q->where('tersedia', '>', 0))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.buku.index', [
            'buku' => $buku,
            'kategori' => KategoriBuku::orderBy('nama')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.buku.form', [
            'buku' => new Buku(['jumlah_eksemplar' => 1, 'tersedia' => 1]),
            'kategori' => KategoriBuku::orderBy('nama')->get(),
        ]);
    }

    public function store(BukuRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['tersedia'] = $data['jumlah_eksemplar'];
        $data['cover'] = $this->simpanCover($request);

        Buku::create($data);

        return redirect()->route('admin.buku.index')->with('success', 'Buku berhasil ditambahkan.');
    }

    public function edit(Buku $buku): View
    {
        return view('admin.buku.form', [
            'buku' => $buku,
            'kategori' => KategoriBuku::orderBy('nama')->get(),
        ]);
    }

    public function update(BukuRequest $request, Buku $buku): RedirectResponse
    {
        $data = $request->validated();

        $dipinjam = $buku->peminjaman()->where('status', 'dipinjam')->count();
        if ($data['jumlah_eksemplar'] < $dipinjam) {
            return back()->withInput()->with('error', "Jumlah eksemplar tidak boleh kurang dari {$dipinjam} buku yang sedang dipinjam.");
        }

        $selisih = $data['jumlah_eksemplar'] - $buku->jumlah_eksemplar;
        $data['tersedia'] = max(0, $buku->tersedia + $selisih);

        if ($cover = $this->simpanCover($request)) {
            if ($buku->cover) {
                Storage::disk('public')->delete($buku->cover);
            }
            $data['cover'] = $cover;
        }

        $buku->update($data);

        return redirect()->route('admin.buku.index')->with('success', 'Buku berhasil diperbarui.');
    }

    public function destroy(Buku $buku): RedirectResponse
    {
        if ($buku->peminjaman()->where('status', 'dipinjam')->exists()) {
            return back()->with('error', 'Buku tidak dapat dihapus karena sedang dipinjam.');
        }

        if ($buku->cover) {
            Storage::disk('public')->delete($buku->cover);
        }

        $buku->delete();

        return redirect()->route('admin.buku.index')->with('success', 'Buku berhasil dihapus.');
    }

    private function simpanCover(Request $request): ?string
    {
        if (! $request->hasFile('cover')) {
            return null;
        }

        return $request->file('cover')->store('cover', 'public');
    }
}
