<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Evento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'eventos';

    protected $fillable = [
        'nombre',
        'descripcion_corta',
        'descripcion_larga',
        'poster_url',
        'fecha',
        'hora',
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora'  => 'datetime:H:i',
    ];

    // ============================================
    // RELACIONES
    // ============================================

    public function precios()
    {
        return $this->hasMany(Precio::class);
    }

    public function sectores()
    {
        return $this->belongsToMany(Sector::class, 'precios')
                    ->withPivot('precio', 'disponible')
                    ->withTimestamps();
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

    public function sectoresDisponibles()
    {
        return $this->sectores()
            ->where('sectores.activo', true)
            ->wherePivot('disponible', true)
            ->get();
    }


    public function precioParaSector(int $sectorId): ?Precio
    {
        return $this->precios()
            ->where('sector_id', $sectorId)
            ->first();
    }

    public function esFuturo(): bool
    {
        return $this->fecha->isFuture();
    }

    public function tieneEntradasVendidas(): bool
    {
        return $this->entradas()->exists();
    }

    // ============================================
    // SCOPES
    // ============================================

    public function scopeFuturos($query)
    {
        return $query->where('fecha', '>=', now()->toDateString());
    }
}
