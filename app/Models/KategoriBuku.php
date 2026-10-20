<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class KategoriBuku extends Model
{
    use HasFactory;

    protected $table = 'kategori_buku';

    protected $fillable = [
        'nama',
        'slug',
        'deskripsi',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $kategori) {
            $kategori->slug = Str::slug($kategori->nama);
        });
    }

    public function buku(): HasMany
    {
        return $this->hasMany(Buku::class, 'kategori_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopePencarian(Builder $query, ?string $kata): Builder
    {
        if (! $kata) {
            return $query;
        }

        return $query->where('nama', 'like', "%{$kata}%");
    }
}
