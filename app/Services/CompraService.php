<?php

namespace App\Services;

use App\Models\Entrada;
use App\Models\EstadoAsiento;
use Illuminate\Support\Facades\DB;

class CompraService
{
    /**
     * Procesar la compra de una o varias reservas
     */
    public function procesarCompra(array $reservasIds, $userId)
    {
        $entradas = [];

        DB::beginTransaction();
        try {
            foreach ($reservasIds as $reservaId) {
                $reserva = $this->obtenerReserva($reservaId, $userId);

                $this->verificarNoExpirada($reserva);

                $precio = $this->obtenerPrecio($reserva);

                $reserva->marcarComoVendido();

                $entrada    = $this->crearEntrada($reserva, $precio, $userId);
                $entradas[] = $entrada;
            }

            DB::commit();

            return collect($entradas)->each->load(['evento', 'asiento.sector']);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Obtener una reserva activa perteneciente al usuario
     */
    private function obtenerReserva($reservaId, $userId)
    {
        return EstadoAsiento::where('id', $reservaId)
            ->where('user_id', $userId)
            ->where('estado', 'bloqueado')
            ->with(['evento', 'asiento.sector'])
            ->firstOrFail();
    }

    /**
     * Lanza excepción si la reserva ha expirado
     */
    private function verificarNoExpirada($reserva)
    {
        if ($reserva->haExpirado()) {
            throw new \Exception('Una de las reservas ha expirado. Vuelve a reservar el asiento.');
        }
    }

    /**
     * Obtener el precio del sector para el evento de la reserva
     */
    private function obtenerPrecio($reserva)
    {
        $precio = $reserva->evento->precioDelSector($reserva->asiento->sector_id);

        if (!$precio) {
            throw new \Exception('No se encontró el precio para el sector del asiento.');
        }

        return $precio;
    }

    /**
     * Crear la entrada (el ticket final con QR)
     */
    private function crearEntrada($reserva, $precio, $userId)
    {
        return Entrada::create([
            'user_id'      => $userId,
            'evento_id'    => $reserva->evento_id,
            'asiento_id'   => $reserva->asiento_id,
            'precio_pagado' => $precio->precio,
        ]);
    }
}
