<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Student;
use App\Models\Group;

class StudentActionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authorized_user_can_fetch_student_details_partial()
    {
        // Crear usuario y estudiante
        $user = User::factory()->create();

        $group = Group::factory()->create(['name' => 'A']);
        $student = Student::factory()->create([
            'names' => 'Testito',
            'paternal_surname' => 'Perez',
            'group_id' => $group->id,
            'estado' => 'ACTIVO'
        ]);

        $response = $this->actingAs($user)->get('/students/' . $student->id . '/details');

        $response->assertStatus(200);
        $response->assertSeeText('Testito');
    }
}
