<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoAsiento extends Model
{
    use HasFactory;

    protected $table = 'estado_asientos';

    protected $fillable = [
        'evento_id',
        'asiento_id',
        'user_id',
        'estado',
        'reservado_hasta',
    ];

    protected $casts = [
        'reservado_hasta' => 'datetime',
    ];

    // ============================================
    // RELACIONES
    // ============================================

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }

    public function asiento()
    {
        return $this->belongsTo(Asiento::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ============================================
    // MÉTODOS ÚTILES
    // ============================================

    public function haExpirado(): bool
    {
        if ($this->estado === 'vendido') {
            return false;
        }
        return $this->reservado_hasta && $this->reservado_hasta->isPast();
    }

    public function estaBloqueado(): bool
    {
        return $this->estado === 'bloqueado' && !$this->haExpirado();
    }

    public function estaVendido(): bool
    {
        return $this->estado === 'vendido';
    }

    public function tiempoRestante(): ?int
    {
        if ($this->estado === 'vendido' || !$this->reservado_hasta) {
            return null;
        }
        $diff = now()->diffInMinutes($this->reservado_hasta, false);
        return $diff > 0 ? $diff : 0;
    }

    public function liberar(): bool
    {
        return $this->delete();
    }

    public function marcarComoVendido(): bool
    {
        $this->estado = 'vendido';
        $this->reservado_hasta = null;
        return $this->save();
    }

    // ============================================
    // SCOPES
    // ============================================

    public function scopeBloqueados($query)
    {
        return $query->where('estado', 'bloqueado');
    }

    public function scopeVendidos($query)
    {
        return $query->where('estado', 'vendido');
    }

    public function scopeExpirados($query)
    {
        return $query->where('estado', 'bloqueado')
                     ->where('reservado_hasta', '<', now());
    }

    public function scopeDeUsuario($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeDeEvento($query, $eventoId)
    {
        return $query->where('evento_id', $eventoId);
    }
}
