<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SiswaRequest;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class SiswaController extends Controller
{
    public function index(Request $request): View
    {
        $siswa = User::query()
            ->siswa()
            ->pencarian($request->string('q')->trim()->value() ?: null)
            ->withCount([
                'peminjaman as sedang_dipinjam_count' => fn ($q) => $q->where('status', Peminjaman::STATUS_DIPINJAM),
                'peminjaman as total_pinjam_count',
            ])
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.siswa.index', ['siswa' => $siswa]);
    }

    public function create(): View
    {
        return view('admin.siswa.form', ['siswa' => new User(['role' => User::ROLE_SISWA])]);
    }

    public function store(SiswaRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['role'] = User::ROLE_SISWA;
        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()->route('admin.siswa.index')->with('success', 'Anggota siswa berhasil ditambahkan.');
    }

    public function edit(User $siswa): View
    {
        abort_unless($siswa->role === User::ROLE_SISWA, 404);

        return view('admin.siswa.form', ['siswa' => $siswa]);
    }

    public function update(SiswaRequest $request, User $siswa): RedirectResponse
    {
        abort_unless($siswa->role === User::ROLE_SISWA, 404);

        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $siswa->update($data);

        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(User $siswa): RedirectResponse
    {
        abort_unless($siswa->role === User::ROLE_SISWA, 404);

        if ($siswa->peminjaman()->where('status', Peminjaman::STATUS_DIPINJAM)->exists()) {
            return back()->with('error', 'Siswa tidak dapat dihapus karena masih memiliki pinjaman aktif.');
        }

        $siswa->delete();

        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil dihapus.');
    }
}
