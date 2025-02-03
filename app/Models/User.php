<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Responsavel;
use App\Models\Empresa;
use App\Models\Ponto;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'responsavel_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the Responsavel associated with the user.
     */
    public function responsavel()
    {
        return $this->belongsTo(Responsavel::class);
    }

    /**
     * Get the overtimes associated with the user.
     */
    public function overtimes()
    {
        return $this->hasMany(Ponto::class)->where('horas_extras', '>', 0);
    }

}
