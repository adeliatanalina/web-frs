<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('waitlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('matkul_id')->constrained('matkuls')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->timestamps();

            // user tidak boleh mengantri kelas yang sama lebih dari sekali
            $table->unique(['user_id', 'kelas_id']);
            // query promosi akan sering by kelas + waktu daftar
            $table->index(['kelas_id', 'created_at']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('waitlists');
    }
};
