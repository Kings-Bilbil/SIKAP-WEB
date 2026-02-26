<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Mengubah JSON di database menjadi Array saat ditarik ke PHP
    protected $casts = [
        'data_isi' => 'array',
    ];

    public function tabulasi()
    {
        return $this->belongsTo(Tabulasi::class);
    }

    public function agenda()
    {
        return $this->belongsTo(Agenda::class);
    }

    public function bidang()
    {
        return $this->belongsTo(Bidang::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}