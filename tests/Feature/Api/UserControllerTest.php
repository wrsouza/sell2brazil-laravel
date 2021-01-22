<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UserControllerTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testApiGetUsersReturnsPaginateUsers()
    {
        User::factory(60)->create();
        $response = $this->get('/api/users');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at'
                ]
            ]
        ]);

        $content = json_decode($response->getContent());
        $this->assertCount(15, $content->data);
        $this->assertEquals(60, $content->total);
        $this->assertEquals(4, $content->last_page);
        $this->assertEquals(15, $content->per_page);
        $this->assertEquals(1, $content->current_page);
    }

    public function testApiGetUsersReturnsEmptyPaginateUsers()
    {
        $response = $this->get('/api/users');
        $response->assertStatus(200);
        $content = json_decode($response->getContent());
        $this->assertEmpty($content->data);
    }

    public function testApiPostUsersReturnsNewUser()
    {
        $data = [
            'name' => 'User Test',
            'email' => 'test@test.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];

        $response = $this->post('/api/users', $data);
        $response->assertStatus(201);
        $response->assertJsonStructure([
            'id',
            'name',
            'email',
            'created_at',
            'updated_at'
        ]);

        $content = json_decode($response->getContent());
        $this->assertEquals(1, $content->id);
        $this->assertEquals($data['name'], $content->name);
        $this->assertEquals($data['email'], $content->email);

        $user = User::find($content->id);
        $this->assertTrue(Hash::check($data['password'], $user->password));
    }

    public function testApiPostUsersWithFailedRequiredFields()
    {
        $data = [];
        $response = $this->post('/api/users', $data);
        $response->assertStatus(400);
        $response->assertExactJson([
            'name' => ['O campo é obrigatório!'],
            'email' => ['O campo é obrigatório!'],
            'password' => ['O campo é obrigatório!'],
        ]);
    }

    public function testApiPostUsersWithFailedMaxCharacters()
    {
        $password = Str::random(21);
        $data = [
            'name' => Str::random(256),
            'email' => 'test@test.com',
            'password' => $password,
            'password_confirmation' => $password
        ];
        $response = $this->post('/api/users', $data);
        $response->assertStatus(400);
        $response->assertExactJson([
            'name' => ['Máximo de 255 caracteres!'],
            'password' => ['Máximo de 20 caracteres!']
        ]);
    }

    public function testApiPostUsersWithFailedMinCharacters()
    {
        $password = Str::random(5);
        $data = [
            'name' => Str::random(4),
            'email' => 'test@test.com',
            'password' => $password,
            'password_confirmation' => $password
        ];
        $response = $this->post('/api/users', $data);
        $response->assertStatus(400);
        $response->assertExactJson([
            'name' => ['Mínimo de 5 caracteres!'],
            'password' => ['Mínimo de 6 caracteres!']
        ]);
    }

    public function testApiPostUsersWithFailedEmailValidation()
    {
        $data = [
            'name' => 'User Test',
            'email' => 'email_invalid',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];
        $response = $this->post('/api/users', $data);
        $response->assertStatus(400);
        $response->assertExactJson([
            'email' => ['E-mail inválido!']
        ]);
    }

    public function testApiPostUsersWithFailedPasswordConfirmation()
    {
        $data = [
            'name' => 'User Test',
            'email' => 'test@test.com',
            'password' => 'password',
        ];
        $response = $this->post('/api/users', $data);
        $response->assertStatus(400);
        $response->assertExactJson([
            'password' => ['A Confirmação de Senha não confere!']
        ]);
    }

    public function testApiPostUsersWithFailedUniqueEmail()
    {
        User::factory()->create(['email' => 'test@test.com']);

        $data = [
            'name' => 'User Test',
            'email' => 'test@test.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];
        $response = $this->post('/api/users', $data);
        $response->assertStatus(400);
        $response->assertExactJson([
            'email' => [$data['email'] . ' já em uso!']
        ]);
    }
}
