<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Peminjaman extends Model
{
    use HasFactory;

    public const STATUS_DIAJUKAN = 'diajukan';

    public const STATUS_DIPINJAM = 'dipinjam';

    public const STATUS_DIKEMBALIKAN = 'dikembalikan';

    protected $table = 'peminjaman';

    protected $fillable = [
        'kode_pinjam',
        'user_id',
        'buku_id',
        'petugas_id',
        'tanggal_pinjam',
        'tanggal_kembali',
        'tanggal_dikembalikan',
        'status',
        'denda',
        'status_bayar',
        'catatan',
    ];

    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_kembali' => 'date',
        'tanggal_dikembalikan' => 'date',
        'denda' => 'decimal:2',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function buku(): BelongsTo
    {
        return $this->belongsTo(Buku::class);
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function isDipinjam(): bool
    {
        return $this->status === self::STATUS_DIPINJAM;
    }

    public function isDiajukan(): bool
    {
        return $this->status === self::STATUS_DIAJUKAN;
    }

    public function isAktif(): bool
    {
        return in_array($this->status, [self::STATUS_DIAJUKAN, self::STATUS_DIPINJAM], true);
    }

    public function isTerlambat(): bool
    {
        return $this->isDipinjam() && $this->tanggal_kembali->isPast()
            || ($this->status === self::STATUS_DIKEMBALIKAN
                && $this->tanggal_dikembalikan
                && $this->tanggal_dikembalikan->gt($this->tanggal_kembali));
    }

    public function hariTerlambat(): int
    {
        $akhir = $this->tanggal_dikembalikan ?? Carbon::today();

        return max(0, $this->tanggal_kembali->diffInDays($akhir, false));
    }

    public function hitungDenda(): int
    {
        return $this->hariTerlambat() * (int) Pengaturan::get('denda_per_hari', 500);
    }

    public static function generateKodePinjam(): string
    {
        do {
            $kode = 'PJM-'.now()->format('Ymd').'-'.strtoupper(substr(uniqid(), -4));
        } while (self::where('kode_pinjam', $kode)->exists());

        return $kode;
    }

    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        if (! $status) {
            return $query;
        }

        return $query->where('status', $status);
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_DIAJUKAN, self::STATUS_DIPINJAM]);
    }

    public function scopeTerlambat(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DIPINJAM)
            ->whereDate('tanggal_kembali', '<', Carbon::today());
    }
}
