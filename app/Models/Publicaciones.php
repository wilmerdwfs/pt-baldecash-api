<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publicaciones extends Model
{
    public $timestamps = false;
    
    use HasFactory;

    protected $table = 'publicaciones';
}
