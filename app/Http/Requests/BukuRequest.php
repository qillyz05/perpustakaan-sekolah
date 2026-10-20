<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BukuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $buku = $this->route('buku');

        return [
            'kode_buku' => ['nullable', 'string', 'max:50', Rule::unique('buku', 'kode_buku')->ignore($buku)],
            'judul' => ['required', 'string', 'max:255'],
            'penulis' => ['required', 'string', 'max:255'],
            'penerbit' => ['nullable', 'string', 'max:255'],
            'tahun_terbit' => ['nullable', 'integer', 'min:1900', 'max:'.(date('Y') + 1)],
            'isbn' => ['nullable', 'string', 'max:30'],
            'kategori_id' => ['required', 'exists:kategori_buku,id'],
            'rak' => ['nullable', 'string', 'max:50'],
            'sinopsis' => ['nullable', 'string', 'max:5000'],
            'jumlah_eksemplar' => ['required', 'integer', 'min:1', 'max:1000'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'kode_buku' => 'kode buku',
            'kategori_id' => 'kategori',
            'jumlah_eksemplar' => 'jumlah eksemplar',
        ];
    }
}
