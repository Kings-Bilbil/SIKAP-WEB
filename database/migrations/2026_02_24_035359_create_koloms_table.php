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
        Schema::create('koloms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tabulasi_id')->constrained('tabulasis')->onDelete('cascade');
            $table->string('nama_kolom'); // cth: 'Jumlah', 'Done Sebelum'
            $table->string('tipe_input'); // cth: 'text', 'number', 'checkbox'
            $table->timestamps();
        });    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('koloms');
    }
};
