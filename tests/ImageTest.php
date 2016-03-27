<?php
use Illuminate\Support\Facades\Log;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\DatabaseMigrations;

class ImageTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * GET /images
     *
     * @return void
     */
    public function testGetEmptyImages()
    {
        // No images
        $response = $this->get('api/v1/images');
        $response->assertResponseOk();
        $response->seeJsonStructure([
            'data' => [],
        ]);
    }

    /**
     * GET /images
     *
     * @return void
     */
    public function testGetImages()
    {
        // Add an image
        $image = factory('App\Image')->create();

        $this->get('api/v1/images')->seeJson([
            'id' => $image->id,
        ]);
    }

    /**
     * GET /images/:id
     *
     * @return void
     */
    public function testGetImageNotFound()
    {
        // Image Not Found
        $response = $this->get('api/v1/images/0');
        $response->seeStatusCode(404);
    }

    /**
     * GET /images/:id
     *
     * @return void
     */
    public function testGetImage()
    {
        // Create an image
        $image = factory('App\Image')->create();

        // Check if image is saved in database
        $this->seeInDatabase('images', ['id' => $image->id]);

        // Find this image
        $response = $this->get('api/v1/images/'.$image->id);
        $response->assertResponseOk();
        $response->seeJson([
            'id' => $image->id,
            'title' => $image->title
        ]);
    }

    /**
     * POST /images
     *
     * @return void
     */
    public function testCreateImageNotAuthorized()
    {
        // Unauthorized
        $response = $this->post('api/v1/images', ['title' => str_random(10)]);
        $response->seeStatusCode(401);
    }

    public function testCreateImageNoFile()
    {
        $user = factory('App\User')->create();

        // POST with no file should result in 422
        $response = $this->actingAs($user)->post('api/v1/images', ['title' => str_random(10)]);
        $response->seeStatusCode(422);
        $response->seeJson(["The asset field is required."]);
    }

    public function testCreateImageInvalidFile()
    {
        $user = factory('App\User')->create();

        // POST with non image file should result in 422
        $uploadedFile = new Symfony\Component\HttpFoundation\File\UploadedFile(__DIR__ . '/assets/helloworld.txt', 'helloworld.txt', 'image/jpeg', 446, null, TRUE);

        $response = $this->actingAs($user)->post('api/v1/images', ['asset' => $uploadedFile]);
        $response->seeStatusCode(422);
        $response->seeJson(["The asset must be an image."]);
    }

    public function testCreateImage()
    {
        // TODO: Error with File::isValid() in test env
        // POST with image file should result in 201

        // $user = factory('App\User')->create();
        // $uploadedFile = new Symfony\Component\HttpFoundation\File\UploadedFile(__DIR__ .'/assets/image.jpg', 'image.jpg', 'image/jpeg', null, null, TRUE);
        //
        // $response = $this->actingAs($user)->call('POST', 'api/v1/images', [], [], ['asset' => $uploadedFile]);
        // $this->assertEquals(201, $response->status());
    }

    /**
     * PUT /images/:id
     *
     * @return void
     */
    public function testUpdateImageNotFound()
    {
        // Update a non existing image
        $response = $this->put('api/v1/images/0');
        $response->seeStatusCode(404);
    }

    public function testUpdateImageAsGuest()
    {
        $image = factory('App\Image')->create();

        // Update as guest
        $response = $this->put('api/v1/images/'.$image->id, ['description' => str_random(50)]);
        $response->seeStatusCode(401);
    }

    public function testUpdateImageNotAuthorized()
    {
        $user = factory('App\User')->create();
        $image = factory('App\Image')->create();

        // Update a non authorized image
        $response = $this->actingAs($user)->put('api/v1/images/'.$image->id, ['description' => str_random(50)]);
        $response->seeStatusCode(401);
    }

    public function testUpdateImage()
    {
        $user = factory('App\User')->create();
        $image = factory('App\Image')->create(['user_id' => $user->id]);
        $new_description = str_random(50);

        // Update an authorized image
        $response = $this->actingAs($user)->put('api/v1/images/'.$image->id, ['description' => $new_description]);
        $response->seeStatusCode(200);
        $response->seeJson(['description' => $new_description]);
        $this->seeInDatabase('images', ['description' => $new_description]);
    }

    /**
     * DELETE /images/:id
     *
     * @return void
     */
    public function testDeleteImageNotFound()
    {
        // Delete a non existing image
        $response = $this->delete('api/v1/images/0');
        $response->seeStatusCode(404);
    }

    public function testDeleteImageAsGuest()
    {
        $image = factory('App\Image')->create();

        // Delete as guest
        $response = $this->delete('api/v1/images/'.$image->id);
        $response->seeStatusCode(401);
    }

    public function testDeleteImageNotAuthorized()
    {
        $user = factory('App\User')->create();
        $image = factory('App\Image')->create();

        // Delete a non authorized image
        $response = $this->actingAs($user)->delete('api/v1/images/' . $image->id);
        $response->seeStatusCode(401);
    }

    public function testDeleteImage()
    {
        $user = factory('App\User')->create();
        $image = factory('App\Image')->create(['user_id' => $user->id]);

        // Delete an authorized image
        $response = $this->actingAs($user)->delete('api/v1/images/'.$image->id);
        $response->seeStatusCode(200);

        $this->notSeeInDatabase('images', ['id' => $image->id]);
        $response->seeJson(['message' => 'Image was deleted']);
    }
}
