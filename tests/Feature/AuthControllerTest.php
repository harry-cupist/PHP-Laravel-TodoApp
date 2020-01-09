<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function testLogin()
    {
        $this->withoutExceptionHandling();
        $data = factory(User::class)->make()->toArray();
        $this->post(route('register'), $data);

        $this->post(route('login'), $data)
            ->assertStatus(200);

        $this->assertCount(1, User::all());
        $this->assertEquals($data['email'], User::first()->email);
    }

    /** @test */
    public function testUnauthorizedLogin()
    {
        $data = factory(User::class)->make()->toArray();
        $this->post(route('register'), $data);

        $this->post(route('login'), array_merge($data, ['email' => 'abcd@abc.com']))
            ->assertStatus(404);
    }

    /** @test */
    public function testLogout()
    {
        parent::authenticate();

        $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->get(route('logout'))
            ->assertStatus(200);
    }
}
