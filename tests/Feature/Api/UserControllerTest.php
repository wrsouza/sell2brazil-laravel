<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

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
}
