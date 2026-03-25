<?php

namespace App\Services;

use App\Models\EstadoAsiento;
use Illuminate\Support\Facades\Log;

class LiberarReservasService
{
    /**
     * Elimina todas las reservas con reservado_hasta < now()
     * Retorna el número de reservas liberadas
     */
    public function liberarExpiradas(): int
    {
        $expiradas = EstadoAsiento::expirados()->get();

        $count = 0;

        foreach ($expiradas as $reserva) {
            $reserva->delete();
            $count++;

            Log::info('Reserva expirada liberada', [
                'reserva_id' => $reserva->id,
                'evento_id'  => $reserva->evento_id,
                'asiento_id' => $reserva->asiento_id,
                'user_id'    => $reserva->user_id,
            ]);
        }

        return $count;
    }

    /**
     * Libera reservas expiradas de un usuario concreto
     */
    public function liberarDeUsuario($userId): int
    {
        $expiradas = EstadoAsiento::expirados()
            ->where('user_id', $userId)
            ->get();

        foreach ($expiradas as $reserva) {
            $reserva->delete();
        }

        return $expiradas->count();
    }
}
