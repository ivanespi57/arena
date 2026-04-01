<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Evento;
use App\Models\Sector;
use App\Models\Asiento;
use App\Models\Precio;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AsientoTest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_listar_asientos_de_un_evento()
    {
        $evento = Evento::factory()->create();
        $sector = Sector::factory()->create(['activo' => true]);
        Asiento::factory()->count(3)->create(['sector_id' => $sector->id]);
        Precio::factory()->create([
            'evento_id'  => $evento->id,
            'sector_id'  => $sector->id,
            'disponible' => true,
        ]);

        $response = $this->getJson("/api/eventos/{$evento->id}/asientos");

        $response->assertStatus(200);
    }

    public function test_puede_listar_asientos_de_un_sector_para_un_evento()
    {
        $evento = Evento::factory()->create();
        $sector = Sector::factory()->create(['activo' => true]);
        Asiento::factory()->count(3)->create(['sector_id' => $sector->id]);
        Precio::factory()->create([
            'evento_id'  => $evento->id,
            'sector_id'  => $sector->id,
            'disponible' => true,
        ]);

        $response = $this->getJson("/api/eventos/{$evento->id}/sectores/{$sector->id}/asientos");

        $response->assertStatus(200);
    }
}
