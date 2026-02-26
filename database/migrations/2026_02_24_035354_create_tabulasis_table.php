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
        Schema::create('tabulasis', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel users (siapa pembuatnya)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); 
            $table->string('judul');
            $table->date('deadline')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tabulasis');
    }
};
