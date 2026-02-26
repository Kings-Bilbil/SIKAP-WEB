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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tabulasi_id')->constrained('tabulasis')->onDelete('cascade');
            $table->foreignId('agenda_id')->constrained('agendas')->onDelete('cascade');
            $table->foreignId('bidang_id')->constrained('bidangs')->onDelete('cascade');
            // user_id nullable karena siapa tahu ada isian manual dari pembuat untuk pengisi yang belum login
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); 
            
            // INI KUNCINYA: Menyimpan data dinamis dalam format JSON
            $table->json('data_isi'); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
