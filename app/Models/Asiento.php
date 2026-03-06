<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asiento extends Model
{
    use HasFactory;

    protected $table = 'asientos';

    protected $fillable = [
        'sector_id',
        'fila',
        'numero',
    ];

    // ============================================
    // RELACIONES
    // ============================================

    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }

    public function estadoAsientos()
    {
        return $this->hasMany(EstadoAsiento::class);
    }

    public function entradas()
    {
        return $this->hasMany(Entrada::class);
    }

    // ============================================
    // MÉTODOS ÚTILES
    // ============================================

    public function nombreCompleto(): string
    {
        return $this->sector->nombre . ' - Fila ' . $this->fila . ' - Asiento ' . $this->numero;
    }

    public function estaDisponibleParaEvento(int $eventoId): bool
    {
        return !EstadoAsiento::where('evento_id', $eventoId)
            ->where('asiento_id', $this->id)
            ->exists();
    }

    public function estaReservadoParaEvento(int $eventoId): bool
    {
        return EstadoAsiento::where('evento_id', $eventoId)
            ->where('asiento_id', $this->id)
            ->where('estado', 'bloqueado')
            ->where('reservado_hasta', '>', now())
            ->exists();
    }

    public function estaVendidoParaEvento(int $eventoId): bool
    {
        return EstadoAsiento::where('evento_id', $eventoId)
            ->where('asiento_id', $this->id)
            ->where('estado', 'vendido')
            ->exists();
    }

    public function estadoParaEvento(int $eventoId): ?EstadoAsiento
    {
        return EstadoAsiento::where('evento_id', $eventoId)
            ->where('asiento_id', $this->id)
            ->first();
    }
}
