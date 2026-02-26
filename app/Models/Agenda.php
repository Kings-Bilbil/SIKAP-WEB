<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    use HasFactory;

    protected $fillable = [
        'tabulasi_id',
        'nama_agenda',
        'deadline', // INI YANG MEMBUAT TENGGAT WAKTU TERSIMPAN
    ];

    public function tabulasi()
    {
        return $this->belongsTo(Tabulasi::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}