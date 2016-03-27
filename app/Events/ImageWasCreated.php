<?php

namespace App\Events;

use App\Image;
use Illuminate\Queue\SerializesModels;

class ImageWasCreated extends Event
{
    public $image;

    use SerializesModels;
    /**
     * Create a new event instance.
     *
     * @param Image $image;
     * @return void
     */
    public function __construct(Image $image)
    {
        $this->image = $image;
    }
}
