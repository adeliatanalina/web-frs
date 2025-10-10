<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('matkuls', function (Blueprint $table) {
            $table->unsignedTinyInteger('sks')->default(3)->after('title'); // 1..255, default 3
        });

        // backfill kalau ada data lama tanpa SKS (optional kalau default cukup)
        DB::table('matkuls')->whereNull('sks')->update(['sks' => 3]);
    }

    public function down(): void {
        Schema::table('matkuls', function (Blueprint $table) {
            $table->dropColumn('sks');
        });
    }
};
