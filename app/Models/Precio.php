<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Precio extends Model
{
    use HasFactory;

    protected $table = 'precios';

    protected $fillable = [
        'evento_id',
        'sector_id',
        'precio',
        'disponible',
    ];

    protected $casts = [
        'precio'     => 'decimal:2',
        'disponible' => 'boolean',
    ];

    // ============================================
    // RELACIONES
    // ============================================

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }

    public function sector()
    {
        return $this->belongsTo(Sector::class);
    }

    // ============================================
    // MÉTODOS ÚTILES
    // ============================================

    public function precioFormateado(): string
    {
        return number_format($this->precio, 2, ',', '.') . ' €';
    }

    public function estaDisponible(): bool
    {
        return $this->disponible && $this->sector->activo;
    }

    // ============================================
    // SCOPES
    // ============================================

    public function scopeDisponibles($query)
    {
        return $query->where('disponible', true)
                     ->whereHas('sector', function ($q) {
                         $q->where('activo', true);
                     });
    }

    public function scopeDeEvento($query, $eventoId)
    {
        return $query->where('evento_id', $eventoId);
    }

    public function scopeDeSector($query, $sectorId)
    {
        return $query->where('sector_id', $sectorId);
    }
}
