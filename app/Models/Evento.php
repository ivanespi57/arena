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

    public function sectorEstaDisponible(int $sectorId): bool
    {
        return $this->sectores()
            ->where('sectores.id', $sectorId)
            ->where('sectores.activo', true)
            ->wherePivot('disponible', true)
            ->exists();
    }

    public function precioDelSector(int $sectorId): ?Precio
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

    public function totalAsientosDisponibles(): int
    {
        return $this->sectoresDisponibles()->sum('capacidad');
    }

    public function totalEntradasVendidas(): int
    {
        return $this->entradas()->count();
    }

    // ============================================
    // SCOPES
    // ============================================

    public function scopeFuturos($query)
    {
        return $query->where('fecha', '>=', now()->toDateString());
    }
}
