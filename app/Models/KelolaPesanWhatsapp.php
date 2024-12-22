<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KelolaPesanWhatsapp extends Model
{
    use HasFactory;

    protected $table = 'kelolapesanwhatsapp';

    protected $fillable = [
        'jenis_pesan', 
        'deskripsi'
    ];
        
}