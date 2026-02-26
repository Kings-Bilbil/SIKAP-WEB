<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('akses_tabulasis', function (Blueprint $table) {
            // Menambahkan kolom status, default-nya 'approved' agar data lama tidak error
            $table->string('status')->default('approved')->after('email_pengisi');
        });
    }

    public function down(): void
    {
        Schema::table('akses_tabulasis', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};