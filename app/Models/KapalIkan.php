<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KapalIkan extends Model
{
    use HasFactory;

    const PENGAJUAN = -1;
    const DITOLAK = 0;
    const DITERIMA = 1;

    protected $guarded = ['id'];
}
