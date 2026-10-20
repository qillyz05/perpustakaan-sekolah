<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengaturan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengaturanController extends Controller
{
    public function edit(): View
    {
        return view('admin.pengaturan.edit', [
            'pengaturan' => [
                'nama_perpustakaan' => Pengaturan::get('nama_perpustakaan', 'Perpustakaan Sekolah'),
                'lama_pinjam_hari' => Pengaturan::get('lama_pinjam_hari', 7),
                'batas_pinjam_maks' => Pengaturan::get('batas_pinjam_maks', 3),
                'denda_per_hari' => Pengaturan::get('denda_per_hari', 500),
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_perpustakaan' => ['required', 'string', 'max:100'],
            'lama_pinjam_hari' => ['required', 'integer', 'min:1', 'max:90'],
            'batas_pinjam_maks' => ['required', 'integer', 'min:1', 'max:20'],
            'denda_per_hari' => ['required', 'integer', 'min:0', 'max:1000000'],
        ]);

        foreach ($data as $key => $value) {
            Pengaturan::set($key, $value);
        }

        return redirect()->route('admin.pengaturan.edit')->with('success', 'Pengaturan berhasil disimpan.');
    }
}
