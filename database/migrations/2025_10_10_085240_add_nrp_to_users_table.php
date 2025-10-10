<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_nrp_to_users_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            // panjang 10, unik
            $table->string('nrp', 10)->nullable()->unique()->after('email');
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('nrp');
        });
    }
};
