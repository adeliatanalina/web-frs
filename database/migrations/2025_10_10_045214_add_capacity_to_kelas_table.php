<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('kelas', function (Blueprint $table) {
            if (!Schema::hasColumn('kelas', 'capacity')) {
                $table->unsignedSmallInteger('capacity')->default(40)->after('title');
            }
        });
    }
    public function down(): void {
        Schema::table('kelas', function (Blueprint $table) {
            $table->dropColumn('capacity');
        });
    }
};
