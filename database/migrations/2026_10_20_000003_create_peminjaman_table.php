<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pinjam')->unique();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('buku_id')->constrained('buku')->restrictOnDelete();
            $table->foreignId('petugas_id')->nullable()->constrained('users')->restrictOnDelete();
            $table->date('tanggal_pinjam');
            $table->date('tanggal_kembali');
            $table->date('tanggal_dikembalikan')->nullable();
            $table->enum('status', ['diajukan', 'dipinjam', 'dikembalikan'])->default('dipinjam')->index();
            $table->decimal('denda', 12, 2)->default(0);
            $table->enum('status_bayar', ['belum_dibayar', 'sudah_dibayar'])->default('belum_dibayar');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index('tanggal_kembali');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
