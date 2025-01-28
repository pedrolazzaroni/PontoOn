<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Responsavel extends Authenticatable
{
    use Notifiable;

    protected $table = 'responsaveis';

    protected $fillable = [
        'name',
        'email',
        'cpf',
        'nome_empresa',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the users associated with the Responsavel.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
