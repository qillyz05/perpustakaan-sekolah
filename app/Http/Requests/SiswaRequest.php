<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $siswa = $this->route('siswa');
        $required = $siswa ? 'nullable' : 'required';

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($siswa)],
            'nis' => [$required, 'nullable', 'string', 'max:30', Rule::unique('users', 'nis')->ignore($siswa)],
            'kelas' => ['nullable', 'string', 'max:50'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'password' => [$required, 'nullable', 'string', 'min:6'],
        ];
    }

    public function attributes(): array
    {
        return ['name' => 'nama', 'nis' => 'NIS'];
    }
}
