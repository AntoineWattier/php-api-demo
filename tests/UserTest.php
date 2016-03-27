<?php
use Illuminate\Support\Facades\Log;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Faker\Factory as Faker;

class UserTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * GET /users/:id
     *
     * @return void
     */
    public function testGetUserNotFound()
    {
        // User Not Found
        $response = $this->get('api/v1/users/0');
        $response->seeStatusCode(404);
    }
//
    /**
     * GET /users/:id
     *
     * @return void
     */
    public function testGetUser()
    {
        // Create an image
        $user = factory('App\User')->create();

        // Check if user is saved in database
        $this->seeInDatabase('users', ['id' => $user->id]);

        // Find this user
        $response = $this->get('api/v1/users/'.$user->id);
        $response->assertResponseOk();
        $response->seeJson([
            'id' => $user->id,
            'firstname' => $user->firstname
        ]);
    }
//
    /**
     * POST /users
     *
     * @return void
     */
    // TODO
    public function testCreateUserInvalidData(){
        $faker = Faker::create();
        $password = $faker->md5;

        $data = [
            'firstname' => '',
            'lastname' => str_random(10),
            'email' => strtolower($faker->safeEmail),
            'password' => $password,
            'password_confirmation' => $password
        ];

        $response = $this->post('api/v1/users', $data);
        $response->seeStatusCode(422);
    }

    public function testCreateUser(){
        $faker = Faker::create();
        $password = $faker->md5;

        $data = [
            'firstname' => str_random(10),
            'lastname' => str_random(10),
            'email' => strtolower($faker->safeEmail),
            'password' => $password,
            'password_confirmation' => $password
        ];

        $response = $this->post('api/v1/users', $data);
        $response->seeStatusCode(201);
    }

    /**
     * PUT /users/:id
     *
     * @return void
     */
    public function testUpdateUserGuest()
    {

        // Update as guest
        $user = factory('App\User')->create();

        $response = $this->put('api/v1/users/'.$user->id, ['firstname' => str_random(50)]);
        $response->seeStatusCode(401);
    }

    public function testUpdateUserNotAuthorized()
    {
        // Update a non authorized users
        $user = factory('App\User')->create();
        $secondUser = factory('App\User')->create();

        $response = $this->actingAs($user)->put('api/v1/users/'.$secondUser->id, ['firstname' => str_random(10)]);
        $response->seeStatusCode(401);
    }

    public function testUpdateUser()
    {
        // Update an authorized user
        $user = factory('App\User')->create();
        $new_firstname = str_random(10);

        $response = $this->actingAs($user)->put('api/v1/users/'.$user->id, ['firstname' => $new_firstname]);
        $response->seeStatusCode(200);
        $response->seeJson(['firstname' => $new_firstname]);
        $this->seeInDatabase('users', ['firstname' => $new_firstname]);
    }

}
