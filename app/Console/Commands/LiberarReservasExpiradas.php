<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\LiberarReservasService;

class LiberarReservasExpiradas extends Command
{
    protected $signature = 'reservas:liberar';
    protected $description = 'Libera las reservas de asientos que han expirado';

    public function handle(LiberarReservasService $service): void
    {
        $liberadas = $service->liberarExpiradas();
        $this->info("Se liberaron {$liberadas} reservas expiradas.");
    }
}
