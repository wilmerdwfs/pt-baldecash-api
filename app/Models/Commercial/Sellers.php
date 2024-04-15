<?php

namespace App\Models\Commercial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sellers extends Model
{
    public $timestamps = false;
    
    use HasFactory;

    protected $table = 'c_clientes';
}
