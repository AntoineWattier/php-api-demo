<?php
use Illuminate\Support\Facades\Log;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\DatabaseMigrations;

class CommentTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * POST /images/:id/comments
     *
     * @return void
     */

    public function testCreateCommentNotAuthorized(){
        // Create Image
        $image = factory('App\Image')->create();

        // Unauthorized
        $response = $this->post('api/v1/images/'.$image->id.'/comments', ['content' => str_random(10)]);
        $response->seeStatusCode(401);

    }

    public function testCreateCommentImageNotFound()
    {
        // Create a comment on a non existing image
        $user = factory('App\User')->create();

        $response = $this->actingAs($user)->post('api/v1/images/1/comments');
        $response->seeStatusCode(404);
    }

    public function testCreateComment()
    {
        // Create Auth user
        $user = factory('App\User')->create();
        $image = factory('App\Image')->create();

        $response = $this->actingAs($user)->post('api/v1/images/'.$image->id.'/comments', ['content' => str_random(10)]);
        $response->seeStatusCode(201);
    }

    /**
     * PUT /comments/:id
     *
     * @return void
     */
    public function testUpdateCommentGuest()
    {
        // Update as guest
        $comment = factory('App\Comment')->create();

        $response = $this->put('api/v1/comments/'.$comment->id, ['content' => str_random(50)]);
        $response->seeStatusCode(401);
    }

    public function testUpdateCommentNotFound()
    {
        // Update a non existing comment
        $response = $this->put('api/v1/comment/0');
        $response->seeStatusCode(404);
    }

    public function testUpdateCommentNotAuthorized()
    {
        // Update a non authorized comment
        $user = factory('App\User')->create();
        $comment = factory('App\Comment')->create();

        $response = $this->actingAs($user)->put('api/v1/comments/'.$comment->id, ['content' => str_random(50)]);
        $response->seeStatusCode(401);
    }

    public function testUpdateComment()
    {
        // Update an authorized comment
        $user = factory('App\User')->create();
        $comment = factory('App\Comment')->create(['user_id' => $user->id]);
        $new_content = str_random(50);

        $response = $this->actingAs($user)->put('api/v1/comments/'.$comment->id, ['content' => $new_content]);
        $response->seeStatusCode(200);
        $response->seeJson(['content' => $new_content]);
        $this->seeInDatabase('comments', ['content' => $new_content]);
    }

    /**
     * DELETE /comments/:id
     *
     * @return void
     */
    public function testDeleteCommentNotFound()
    {
        $user = factory('App\User')->create();

        // Delete a non existing comment
        $response = $this->actingAs($user)->delete('api/v1/comments/0');
        $response->seeStatusCode(404);
    }

    public function testDeleteCommentAsGuest()
    {
        $comment = factory('App\Comment')->create();

        // Delete as guest
        $response = $this->delete('api/v1/comments/'.$comment->id);
        $response->seeStatusCode(401);
    }

    public function testDeleteImageNotAuthorized()
    {
        $user = factory('App\User')->create();
        $comment = factory('App\Comment')->create();

        // Delete a non authorized comment
        $response = $this->actingAs($user)->delete('api/v1/comments/' . $comment->id);
        $response->seeStatusCode(401);
    }

    public function testDeleteComment()
    {
        $user = factory('App\User')->create();
        $comment = factory('App\Comment')->create(['user_id' => $user->id]);

        // Delete an authorized comment
        $response = $this->actingAs($user)->delete('api/v1/comments/'.$comment->id);
        $response->seeStatusCode(200);

        $this->notSeeInDatabase('comments', ['id' => $comment->id]);
        $response->seeJson(['message' => 'Comment was deleted']);
    }
}
