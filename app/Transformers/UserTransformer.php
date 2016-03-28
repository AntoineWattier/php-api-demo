<?php 

namespace App\Transformers;

use App\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract {

    protected $availableIncludes = [
        'images'
    ];

    public function transform(User $user)
    {
        return [
            'id'           => $user->id,
            'firstname'    => $user->firstname,
            'lastname'     => $user->lastname,
            'created_at'   => $user->created_at->toDateTimeString(),
            'updated_at'   => $user->updated_at->toDateTimeString()
        ];
    }

    public function includeImages(User $user)
    {
        $images = $user->images->sortByDesc('id');

        return $this->collection($images, new ImageTransformer);
    }
}