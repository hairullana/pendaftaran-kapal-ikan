<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicToken extends Model
{
    use HasFactory;

    protected $table = 'public_token';

    protected $guarded = ['id'];
}
