<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\KategoriRequest;
use App\Models\KategoriBuku;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class KategoriController extends Controller
{
    public function index(): View
    {
        return view('admin.kategori.index', [
            'kategori' => KategoriBuku::withCount('buku')->orderBy('nama')->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('admin.kategori.form', ['kategori' => new KategoriBuku]);
    }

    public function store(KategoriRequest $request): RedirectResponse
    {
        KategoriBuku::create($request->validated());

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(KategoriBuku $kategori): View
    {
        return view('admin.kategori.form', ['kategori' => $kategori]);
    }

    public function update(KategoriRequest $request, KategoriBuku $kategori): RedirectResponse
    {
        $kategori->update($request->validated());

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(KategoriBuku $kategori): RedirectResponse
    {
        if ($kategori->buku()->exists()) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki buku.');
        }

        $kategori->delete();

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
