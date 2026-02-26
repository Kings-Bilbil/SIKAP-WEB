<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AksesTabulasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'tabulasi_id',
        'bidang_id',
        'email_pengisi',
        'status',
        'peran', // Tambahan Baru
    ];

    public function tabulasi()
    {
        return $this->belongsTo(Tabulasi::class);
    }

    public function bidang()
    {
        return $this->belongsTo(Bidang::class);
    }
}