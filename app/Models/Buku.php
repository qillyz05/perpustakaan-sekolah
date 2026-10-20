<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Buku extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'buku';

    protected $fillable = [
        'kode_buku',
        'judul',
        'penulis',
        'penerbit',
        'tahun_terbit',
        'isbn',
        'kategori_id',
        'cover',
        'sinopsis',
        'rak',
        'jumlah_eksemplar',
        'tersedia',
    ];

    protected $casts = [
        'tahun_terbit' => 'integer',
        'jumlah_eksemplar' => 'integer',
        'tersedia' => 'integer',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriBuku::class, 'kategori_id');
    }

    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class);
    }

    public function isTersedia(): bool
    {
        return $this->tersedia > 0;
    }

    public static function generateKodeBuku(): string
    {
        do {
            $kode = 'BK-'.now()->format('y').'-'.strtoupper(substr(uniqid(), -5));
        } while (self::withTrashed()->where('kode_buku', $kode)->exists());

        return $kode;
    }

    public function scopePencarian(Builder $query, ?string $kata): Builder
    {
        if (! $kata) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($kata) {
            $q->where('judul', 'like', "%{$kata}%")
                ->orWhere('penulis', 'like', "%{$kata}%")
                ->orWhere('penerbit', 'like', "%{$kata}%")
                ->orWhere('isbn', 'like', "%{$kata}%")
                ->orWhere('kode_buku', 'like', "%{$kata}%");
        });
    }

    public function scopeKategoriSlug(Builder $query, ?string $slug): Builder
    {
        if (! $slug) {
            return $query;
        }

        return $query->whereHas('kategori', fn (Builder $q) => $q->where('slug', $slug));
    }
}
