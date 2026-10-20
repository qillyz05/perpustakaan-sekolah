<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_PUSTAKAWAN = 'pustakawan';

    public const ROLE_SISWA = 'siswa';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'nis',
        'kelas',
        'telepon',
        'alamat',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class);
    }

    public function peminjamanDipinjam(): HasMany
    {
        return $this->peminjaman()->where('status', Peminjaman::STATUS_DIPINJAM);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_PUSTAKAWAN;
    }

    public function scopeSiswa(Builder $query): Builder
    {
        return $query->where('role', self::ROLE_SISWA);
    }

    public function scopePencarian(Builder $query, ?string $kata): Builder
    {
        if (! $kata) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($kata) {
            $q->where('name', 'like', "%{$kata}%")
                ->orWhere('email', 'like', "%{$kata}%")
                ->orWhere('nis', 'like', "%{$kata}%")
                ->orWhere('kelas', 'like', "%{$kata}%");
        });
    }
}
