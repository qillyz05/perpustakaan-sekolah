<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buku', function (Blueprint $table) {
            $table->id();
            $table->string('kode_buku')->unique();
            $table->string('judul');
            $table->string('penulis');
            $table->string('penerbit')->nullable();
            $table->smallInteger('tahun_terbit')->nullable();
            $table->string('isbn')->nullable();
            $table->foreignId('kategori_id')->constrained('kategori_buku')->restrictOnDelete();
            $table->string('cover')->nullable();
            $table->text('sinopsis')->nullable();
            $table->string('rak')->nullable();
            $table->unsignedInteger('jumlah_eksemplar')->default(1);
            $table->unsignedInteger('tersedia')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['judul', 'penulis']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buku');
    }
};
