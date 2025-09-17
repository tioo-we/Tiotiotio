<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            // Cek apakah kolom sudah ada atau belum
            if (!Schema::hasColumn('produks', 'harga_jual')) {
                $table->decimal('harga_jual', 15, 2)->after('harga_produk')->default(0);
            }
            
            // Pastikan kolom diskon juga ada
            if (!Schema::hasColumn('produks', 'diskon')) {
                $table->integer('diskon')->after('harga_jual')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            if (Schema::hasColumn('produks', 'harga_jual')) {
                $table->dropColumn('harga_jual');
            }
            
            if (Schema::hasColumn('produks', 'diskon')) {
                $table->dropColumn('diskon');
            }
        });
    }
};