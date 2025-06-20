<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create a user to act as an authenticated user for tests
        // as the routes are protected by auth:sanctum
        $this->actingAs(User::factory()->createOne(), 'sanctum');
    }

    /**
     * @test
     */
    public function index_returns_a_list_of_users()
    {
        User::factory()->count(3)->create();

        $response = $this->getJson(route('users.index'));

        $response->assertOk();
        $response->assertJsonCount(User::count()); // Total users including the authenticated one
        $response->assertJsonStructure([
            '*' => [
                'id',
                'nickname',
                'avatar',
            ]
        ]);
    }

    /**
     * @test
     */
    public function show_returns_a_specific_user()
    {
        $user = User::factory()->create();

        $response = $this->getJson(route('users.show', $user->id));

        $response->assertOk();
        $response->assertJson([
            'id' => $user->id,
            'nickname' => $user->name,
            'avatar' => $user->avatar,
        ]);
    }

    /**
     * @test
     */
    public function show_returns_404_if_user_not_found()
    {
        $nonExistentUserId = 9999;
        $response = $this->getJson(route('users.show', $nonExistentUserId));

        $response->assertNotFound();
        $response->assertJson(['error' => 'User with the given id is not found']);
    }

    /**
     * @test
     */
    public function search_returns_users_matching_query()
    {
        User::factory()->create(['name' => 'Pavel Test User']);
        User::factory()->create(['name' => 'Another User']);
        User::factory()->create(['name' => 'Pavel Second']);

        $response = $this->getJson(route('users.search', ['s' => 'Pavel']));

        $response->assertOk();
        $response->assertJsonStructure([
            'count',
            'data' => [
                '*' => [
                    'id',
                    'nickname',
                    'avatar',
                ]
            ]
        ]);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonPath('count', 2);
        $responseData = $response->json('data');
        $this->assertTrue(collect($responseData)->contains('nickname', 'Pavel Test User'));
        $this->assertTrue(collect($responseData)->contains('nickname', 'Pavel Second'));
    }

    /**
     * @test
     */
    public function search_returns_empty_data_if_no_users_match()
    {
        User::factory()->create(['name' => 'Pavel Test User', 'name' => 'pavel_test']);

        $response = $this->getJson(route('users.search', ['s' => 'NonExistentName']));

        $response->assertOk();
        $response->assertJsonStructure([
            'count',
            'data'
        ]);
        $response->assertJsonCount(0, 'data');
        $response->assertJsonPath('count', 0);
    }

    /**
     * @test
     */
    public function search_returns_error_if_search_string_is_missing()
    {
        $response = $this->getJson(route('users.search'));

        $response->assertOk(); // The controller returns a 200 with an error message in this case
        $response->assertJson(['error' => 'Missing search string']);
    }
}
