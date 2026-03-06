<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Entrada extends Model
{
    use HasFactory;

    protected $table = 'entradas';

    protected $fillable = [
        'user_id',
        'evento_id',
        'asiento_id',
        'precio_pagado',
        'codigo_qr',
    ];

    protected $casts = [
        'precio_pagado' => 'decimal:2',
    ];

    // ============================================
    // RELACIONES
    // ============================================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }

    public function asiento()
    {
        return $this->belongsTo(Asiento::class);
    }

    // ============================================
    // MÉTODOS ÚTILES
    // ============================================

    public static function generarCodigoQR(): string
    {
        do {
            $codigo = 'QR-' . strtoupper(Str::random(12));
        } while (self::where('codigo_qr', $codigo)->exists());

        return $codigo;
    }

    public function precioFormateado(): string
    {
        return number_format($this->precio_pagado, 2, ',', '.') . ' €';
    }

    public function informacionCompleta(): array
    {
        return [
            'codigo_qr' => $this->codigo_qr,
            'evento'    => $this->evento->nombre,
            'fecha'     => $this->evento->fecha->format('d/m/Y'),
            'hora'      => $this->evento->hora,
            'asiento'   => $this->asiento->nombreCompleto(),
            'precio'    => $this->precioFormateado(),
            'comprador' => $this->user->nombre . ' ' . $this->user->apellido,
        ];
    }

    public function esValida(): bool
    {
        return !$this->evento->yaPaso();
    }

    // ============================================
    // SCOPES
    // ============================================

    public function scopeDeUsuario($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeDeEvento($query, $eventoId)
    {
        return $query->where('evento_id', $eventoId);
    }

    public function scopeValidas($query)
    {
        return $query->whereHas('evento', function ($q) {
            $q->where('fecha', '>=', now()->toDateString());
        });
    }

    // ============================================
    // BOOT
    // ============================================

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($entrada) {
            if (!$entrada->codigo_qr) {
                $entrada->codigo_qr = self::generarCodigoQR();
            }
        });
    }
}
