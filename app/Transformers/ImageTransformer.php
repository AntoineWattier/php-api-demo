<?php 

namespace App\Transformers;

use App\Image;
use League\Fractal\TransformerAbstract;

class ImageTransformer extends TransformerAbstract {

    protected $availableIncludes = [
        'comments', 'user'
    ];

    public function transform(Image $image)
    {

        return [
            'id'           => $image->id,
            'image_url'    => $image->getFilePath('original').$image->original_filename,
            'title'        => $image->title,
            'description'  => $image->description,
            'filesize'     => $image->filesize,
            'mimetype'     => $image->mime,
            'created_at'   => $image->created_at->toDateTimeString(),
            'updated_at'   => $image->updated_at->toDateTimeString()
        ];
    }

    public function includeComments(Image $image)
    {
        $comments = $image->comments;

        return $this->collection($comments, new CommentTransformer);
    }

    public function includeUser(Image $image)
    {
        $user = $image->user;

        return $this->item($user, new UserTransformer);
    }
}