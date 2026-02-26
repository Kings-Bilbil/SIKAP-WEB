<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tabulasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'judul',
        'link_unik', // INI YANG MEMBUAT LINK GDOCS-NYA TERSIMPAN
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function agendas()
    {
        return $this->hasMany(Agenda::class);
    }

    public function bidangs()
    {
        return $this->hasMany(Bidang::class);
    }

    public function koloms()
    {
        return $this->hasMany(Kolom::class);
    }

    public function akses_tabulasis()
    {
        return $this->hasMany(AksesTabulasi::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}