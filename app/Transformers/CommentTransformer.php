<?php 

namespace App\Transformers;

use App\Comment;
use League\Fractal\TransformerAbstract;

class CommentTransformer extends TransformerAbstract {

    public function transform(Comment $comment)
    {

        return [
            'id'           => $comment->id,
            'image_id'     => $comment->image_id,
            'author'       =>  [ 'firstname' => $comment->user->firstname,
                                 'lastname'  => $comment->user->lastname ],
            'content'      => $comment->content,
            'created_at'   => $comment->created_at->toDateTimeString(),
            'updated_at'   => $comment->updated_at->toDateTimeString()
        ];
    }
}