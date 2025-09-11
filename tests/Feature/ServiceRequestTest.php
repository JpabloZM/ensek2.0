<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Service;

class ServiceRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_submit_service_request()
    {
        // Crear un usuario
        $user = User::factory()->create(['role_id' => 3]); // role_id 3 = Cliente
        
        // Crear un servicio
        $service = Service::factory()->create();
        
        // Actuar como el usuario
        $this->actingAs($user);
        
        // Enviar solicitud de servicio
        $response = $this->post('/service-request', [
            'service_id' => $service->id,
            'description' => 'Esta es una descripción de prueba',
            'address' => 'Dirección de prueba',
        ]);
        
        // Verificar redirección
        $response->assertRedirect('/thank-you');
        
        // Verificar que se ha creado la solicitud en la base de datos
        $this->assertDatabaseHas('service_requests', [
            'user_id' => $user->id,
            'service_id' => $service->id,
            'description' => 'Esta es una descripción de prueba',
            'address' => 'Dirección de prueba',
            'client_name' => $user->name,
            'client_email' => $user->email,
        ]);
    }
}
