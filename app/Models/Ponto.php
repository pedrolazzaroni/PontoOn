<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ponto extends Model
{
    protected $fillable = ['user_id', 'entrada', 'saida'];
    protected $dates = ['entrada', 'saida'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
