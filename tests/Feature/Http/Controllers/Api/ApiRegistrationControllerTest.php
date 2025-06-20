<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ApiRegistrationControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function signup_creates_user_and_returns_token_successfully()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson(route('api.auth.signUp'), $userData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'nickname',
                    'email',
                ],
                'token',
            ])
            ->assertJson([
                'user' => [
                    'nickname' => 'Test User',
                    'email' => 'test@example.com',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);

        $user = User::where('email', 'test@example.com')->first();
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    /**
     * @test
     */
    public function signup_fails_with_validation_errors()
    {
        $response = $this->postJson(route('api.auth.signUp'), [
            'name' => '',
            'email' => 'not-an-email',
            'password' => 'short',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email']);
    }

    /**
     * @test
     */
    public function signup_fails_if_email_already_exists()
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $userData = [
            'name' => 'Another User',
            'email' => 'existing@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson(route('api.auth.signUp'), $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * @test
     */
    public function login_succeeds_with_correct_credentials()
    {
        $user = User::factory()->create([
            'email' => 'login@example.com',
            'password' => Hash::make('password123'),
        ]);

        $credentials = [
            'email' => 'login@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson(route('api.auth.login'), $credentials);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'nickname',
                    'email',
                ],
                'token',
            ])
            ->assertJson([
                'user' => [
                    'id' => $user->id,
                    'nickname' => $user->name,
                    'email' => $user->email,
                ],
            ]);
    }

    /**
     * @test
     */
    public function login_fails_with_incorrect_password()
    {
        User::factory()->create([
            'email' => 'login@example.com',
            'password' => Hash::make('password123'),
        ]);

        $credentials = [
            'email' => 'login@example.com',
            'password' => 'wrongpassword',
        ];

        $response = $this->postJson(route('api.auth.login'), $credentials);

        $response->assertStatus(200)
            ->assertJson([
                'errors' => [
                    'email' => 'The provided credentials do not match our records.'
                ]
            ]);
    }

    /**
     * @test
     */
    public function login_fails_with_non_existent_email()
    {
        $credentials = [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ];

        $response = $this->postJson(route('api.auth.login'), $credentials);

        $response->assertStatus(200)
            ->assertJson([
                'errors' => [
                    'email' => 'The provided credentials do not match our records.'
                ]
            ]);
    }

    /**
     * @test
     */
    public function logout_succeeds_for_authenticated_user()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson(route('api.auth.logout'));

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertCount(0, $user->tokens);
    }

    /**
     * @test
     */
    public function check_user_returns_true_for_authenticated_user()
    {
        $user = User::factory()->createOne();
        $this->actingAs($user, 'sanctum');

        $response = $this->getJson(route('api.auth.checkUser'));

        $response->assertStatus(200)
            ->assertSee('true');
    }

    /**
     * @test
     */
    public function check_user_returns_false_for_unauthenticated_user()
    {
        $response = $this->getJson(route('api.auth.checkUser'));
        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function get_user_returns_user_data_for_authenticated_user()
    {
        $user = User::factory()->createOne();
        $this->actingAs($user, 'sanctum');

        $response = $this->getJson(route('api.auth.getUser'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email'],
                'token',
            ])
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonPath('user.email', $user->email);
    }
}
