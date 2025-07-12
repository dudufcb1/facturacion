<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role'

    ];
    /*hola*/

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Verificar si el usuario puede anular facturas
     * Solo administradores y propietarios pueden anular facturas
     */
    public function canVoidInvoices(): bool
    {
        return in_array($this->role, ['administrador', 'propietario']);
    }

    /**
     * Verificar si el usuario es administrador
     */
    public function isAdmin(): bool
    {
        return $this->role === 'administrador';
    }

    /**
     * Verificar si el usuario es propietario
     */
    public function isOwner(): bool
    {
        return $this->role === 'propietario';
    }

    /**
     * Verificar si el usuario es operador (roles sin permisos de anulación)
     */
    public function isOperator(): bool
    {
        return in_array($this->role, ['cajero', 'contralor', 'contador']);
    }

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
}
