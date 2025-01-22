<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Relatorio extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'data_inicio', 'data_fim', 'total_horas'];
}
