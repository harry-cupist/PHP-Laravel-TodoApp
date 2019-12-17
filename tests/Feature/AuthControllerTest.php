<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function testRegister()
    {
        $data = factory(User::class)->make()->toArray();

        $this->post(route('register'), $data)
            ->assertStatus(201);

        $this->assertCount(1, User::all());
        $this->assertEquals($data['email'], User::first()->email);
    }

    /** @test */
    public function testRegisterWithoutEmail()
    {
        $data = factory(User::class)->make()->toArray();

        $this->post(route('register'), array_merge($data, ['email' => '']))
            ->assertStatus(400);
    }

    /** @test */
    public function testRegisterWithoutPassword()
    {
        $data = factory(User::class)->make()->toArray();

        $this->post(route('register'), array_merge($data, ['password' => '']))
            ->assertStatus(400);
    }

    /** @test */
    public function testLogin()
    {
        $data = factory(User::class)->make()->toArray();
        $this->post(route('register'), $data);

        $response = $this->post(route('login'), $data)
            ->assertStatus(200);

        $this->assertCount(1, User::all());
        $this->assertArrayHasKey('access_token', $response->json());
        $this->assertEquals($data['email'], User::first()->email);
    }

    /** @test */
    public function testLoginWIthInvalidEmail()
    {
        $data = factory(User::class)->make()->toArray();
        $this->post(route('register'), $data);

        $response = $this->post(route('login'), array_merge($data, ['email' => 'abcd']))
            ->assertStatus(401);
    }

    /** @test */
    public function testLoginWithInvalidPassword()
    {
        $data = factory(User::class)->make()->toArray();
        $this->post(route('register'), $data);

        $response = $this->post(route('login'), array_merge($data, ['password' => 123123]))
            ->assertStatus(401);
    }

    /** @test */
    public function testGetUser()
    {
        $data = factory(User::class)->make()->toArray();
        $this->post(route('register'), $data);

        $token = $this->post(route('login'), $data)
            ->getData()->access_token;

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->post(route('user'))
            ->assertStatus(200);

        $this->assertEquals($data['email'], $response->getData()->data->email);
    }

    /** @test */
    public function testGetUserWithInvalidToken()
    {
        $data = factory(User::class)->make()->toArray();
        $this->post(route('register'), $data);

        $token = $this->post(route('login'), $data)
            ->assertStatus(200)
            ->getData()->access_token;

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . '1111111'])
            ->post(route('user'))
            ->assertStatus(401);
    }

    /** @test */
    public function testLogout()
    {
        $data = factory(User::class)->make()->toArray();
        $this->post(route('register'), $data);

        $token = $this->post(route('login'), $data)
            ->getData()->access_token;

        $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->get(route('logout'), $data)
            ->assertStatus(200);
    }
}