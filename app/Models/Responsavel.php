<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Responsavel extends Model
{
    use HasFactory;
    protected $table = 'responsaveis';

    protected $fillable = [
        'user_id',
        'name',
        'cpf',
        'email',
        'password',
        'nome_empresa',
        'is_admin',
    ];

    /**
     * Get the users associated with the Responsavel.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
