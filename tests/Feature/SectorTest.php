<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Sector;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SectorTest extends TestCase
{
    use RefreshDatabase;

    public function test_puede_listar_sectores()
    {
        Sector::factory()->count(3)->create(['activo' => true]);

        $response = $this->getJson('/api/sectores');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_admin_puede_crear_sector()
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->postJson('/api/admin/sectores', [
            'nombre'      => 'Sector Test',
            'descripcion' => 'Descripción del sector',
            'activo'      => true,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('sectores', [
            'nombre' => 'Sector Test',
        ]);
    }

    public function test_usuario_normal_no_puede_crear_sector()
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->postJson('/api/admin/sectores', [
            'nombre'      => 'Sector Test',
            'descripcion' => 'Descripción del sector',
            'activo'      => true,
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_puede_actualizar_sector()
    {
        $admin  = User::factory()->create(['is_admin' => true]);
        $sector = Sector::factory()->create();

        $response = $this->actingAs($admin)->putJson("/api/admin/sectores/{$sector->id}", [
            'nombre' => 'Sector Actualizado',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('sectores', [
            'id'     => $sector->id,
            'nombre' => 'Sector Actualizado',
        ]);
    }
}
