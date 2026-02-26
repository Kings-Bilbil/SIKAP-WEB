<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('akses_tabulasis', function (Blueprint $table) {
            $table->string('peran')->default('anggota')->after('email_pengisi');
            // Membuat bidang_id boleh kosong (null) karena Ketua Panitia tidak terikat 1 bidang
            $table->unsignedBigInteger('bidang_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('akses_tabulasis', function (Blueprint $table) {
            $table->dropColumn('peran');
            $table->unsignedBigInteger('bidang_id')->nullable(false)->change();
        });
    }
};