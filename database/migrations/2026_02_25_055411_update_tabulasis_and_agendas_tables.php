<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambahkan link unik ke tabulasi & hapus deadline
        Schema::table('tabulasis', function (Blueprint $table) {
            $table->string('link_unik', 15)->unique()->nullable()->after('id');
            $table->dropColumn('deadline');
        });

        // Beri link acak untuk data tabulasi yang sudah ada agar tidak error
        $tabulasis = DB::table('tabulasis')->get();
        foreach ($tabulasis as $tab) {
            DB::table('tabulasis')->where('id', $tab->id)->update(['link_unik' => Str::random(10)]);
        }

        // 2. Tambahkan deadline ke masing-masing agenda
        Schema::table('agendas', function (Blueprint $table) {
            $table->date('deadline')->nullable()->after('nama_agenda');
        });
    }

    public function down(): void
    {
        Schema::table('tabulasis', function (Blueprint $table) {
            $table->dropColumn('link_unik');
            $table->date('deadline')->nullable();
        });
        Schema::table('agendas', function (Blueprint $table) {
            $table->dropColumn('deadline');
        });
    }
};