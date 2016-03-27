<?php

namespace App\Http\Controllers;

use Auth;
use Fractal;
use Log;
use App\Comment;
use App\Image;
use App\Transformers\CommentTransformer;
use Illuminate\Http\Request;

class CommentController extends ApiController
{

    protected $validationRules = [
        'content'      => 'required'
    ];

    protected $comment;

    /**
     * Create a new comment instance.
     *
     * @return void
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }


    /**
     * POST /images/:id/comment
     *
     * @param Request $request
     * @param string  $id
     * @return mixed
     */
    public function create(Request $request, $id)
    {
        // Verify if the target image exists
        Image::findOrFail($id);

        // Validate request data
        $this->validate($request, $this->validationRules);

        $this->comment->image_id = $id;

        $this->comment->user_id = $request->user()->id;
        $this->comment->content = $request->get('content');

        $this->comment->save();

        $data = Fractal::item($this->comment)
                            ->transformWith(new CommentTransformer)
                            ->toArray();

        return $this->respondCreated($data);
    }

    /**
     * PUT /comments/:id
     *
     * @param Request $request
     * @param string  $id
     * @return mixed
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, $this->validationRules);

        $comment = $this->comment->findOrFail($id);

        if (!is_null(Auth::user()) && Auth::user()->can('manage-comment', $comment)) {
            $comment->content = $request->get('content');
            $comment->save();

            $data = Fractal::item($comment)
                ->transformWith(new CommentTransformer)
                ->toArray();

            return $this->respond($data);
        } else {
            return $this->respondUnauthorized();
        }
    }

    /**
     * DELETE /comments/:id
     *
     * @param  string  $id
     * @return Response
     */
    public function delete($id)
    {
        $comment = $this->comment->findOrFail($id);

        if (!is_null(Auth::user()) && Auth::user()->can('manage-comment', $comment)) {
            $comment->delete();

            return $this->respondOk('Comment was deleted');
        } else {
            return $this->respondUnauthorized();
        }
    }

}
