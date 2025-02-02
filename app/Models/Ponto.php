<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class Ponto extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'entrada', 'saida', 'horas_extras'];
    protected $dates = ['entrada', 'saida'];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
