<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KategoriRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $kategori = $this->route('kategori');

        return [
            'nama' => ['required', 'string', 'max:100', Rule::unique('kategori_buku', 'nama')->ignore($kategori)],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
