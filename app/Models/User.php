<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'password',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_admin'          => 'boolean',
        ];
    }

    // ============================================
    // RELACIONES
    // ============================================

    /**
     * Un usuario tiene muchas reservas (estados de asientos)
     */
    public function reservas()
    {
        return $this->hasMany(EstadoAsiento::class);
    }

    /**
     * Un usuario tiene muchas entradas compradas
     */
    public function entradas()
    {
        return $this->hasMany(Entrada::class);
    }

    // ============================================
    // MÉTODOS ÚTILES
    // ============================================

    /**
     * Obtener reservas activas (no expiradas)
     */
    public function reservasActivas()
    {
        return $this->reservas()
            ->where('estado', 'bloqueado')
            ->where('reservado_hasta', '>', now())
            ->get();
    }

    /**
     * Verificar si el usuario es administrador
     */
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }
}
