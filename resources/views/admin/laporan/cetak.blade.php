<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Peminjaman {{ $mulai->format('d-m-Y') }} s/d {{ $sampai->format('d-m-Y') }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; color: #1e293b; margin: 32px; }
        h1 { font-size: 18px; margin: 0; }
        .muted { color: #64748b; font-size: 12px; }
        .header { text-align: center; border-bottom: 2px solid #1e293b; padding-bottom: 12px; margin-bottom: 20px; }
        .meta { display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 16px; }
        .cards { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
        .card { border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 14px; min-width: 110px; }
        .card .label { font-size: 10px; text-transform: uppercase; color: #64748b; }
        .card .value { font-size: 16px; font-weight: bold; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: left; }
        th { background: #f1f5f9; text-transform: uppercase; font-size: 10px; letter-spacing: .03em; }
        tfoot td { font-weight: bold; background: #f8fafc; }
        .footer { margin-top: 28px; display: flex; justify-content: flex-end; font-size: 12px; }
        .sign { text-align: center; width: 200px; }
        .sign .space { height: 60px; }
        @media print { .no-print { display: none; } body { margin: 12px; } }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="margin-bottom:16px">
        <button onclick="window.print()" style="padding:8px 16px;border:1px solid #cbd5e1;border-radius:6px;background:#fff;cursor:pointer">Cetak Ulang</button>
        <a href="{{ route('admin.laporan.index', request()->only(['mulai', 'sampai'])) }}" style="margin-left:8px;font-size:13px;color:#2563eb">Kembali</a>
    </div>

    <div class="header">
        <h1>LAPORAN PEMINJAMAN BUKU</h1>
        <p class="muted">Perpustakaan Sekolah</p>
    </div>

    <div class="meta">
        <span>Periode: <strong>{{ $mulai->translatedFormat('d F Y') }}</strong> s/d <strong>{{ $sampai->translatedFormat('d F Y') }}</strong></span>
        <span>Dicetak: {{ now()->translatedFormat('d F Y H:i') }}</span>
    </div>

    <div class="cards">
        <div class="card"><div class="label">Total</div><div class="value">{{ $ringkasan['total'] }}</div></div>
        <div class="card"><div class="label">Dipinjam</div><div class="value">{{ $ringkasan['dipinjam'] }}</div></div>
        <div class="card"><div class="label">Dikembalikan</div><div class="value">{{ $ringkasan['dikembalikan'] }}</div></div>
        <div class="card"><div class="label">Terlambat</div><div class="value">{{ $ringkasan['terlambat'] }}</div></div>
        <div class="card"><div class="label">Total Denda</div><div class="value">Rp {{ number_format((float) $ringkasan['total_denda'], 0, ',', '.') }}</div></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Siswa</th>
                <th>NIS</th>
                <th>Judul Buku</th>
                <th>Tgl Pinjam</th>
                <th>Tenggat</th>
                <th>Dikembalikan</th>
                <th>Status</th>
                <th>Denda</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($peminjaman as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->kode_pinjam }}</td>
                    <td>{{ $item->siswa->name }}</td>
                    <td>{{ $item->siswa->nis ?: '-' }}</td>
                    <td>{{ $item->buku->judul }}</td>
                    <td>{{ $item->tanggal_pinjam->format('d/m/Y') }}</td>
                    <td>{{ $item->tanggal_kembali->format('d/m/Y') }}</td>
                    <td>{{ $item->tanggal_dikembalikan?->format('d/m/Y') ?? '-' }}</td>
                    <td>{{ $item->isDipinjam() && $item->tanggal_kembali->isPast() ? 'Terlambat' : ucfirst($item->status) }}</td>
                    <td>{{ $item->denda > 0 ? number_format((float) $item->denda, 0, ',', '.') : '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="10" style="text-align:center">Tidak ada data pada periode ini.</td></tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="9" style="text-align:right">Total Denda</td>
                <td>{{ number_format((float) $ringkasan['total_denda'], 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <div class="sign">
            <p>Mengetahui,</p>
            <p>Kepala Pustakawan</p>
            <div class="space"></div>
            <p style="border-top:1px solid #1e293b;padding-top:4px">(..................................)</p>
        </div>
    </div>
</body>
</html>
