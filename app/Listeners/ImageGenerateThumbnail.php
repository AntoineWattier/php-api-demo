<?php

namespace App\Listeners;

use Image;
use App\Events\ImageWasCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\File;

class ImageGenerateThumbnail implements ShouldQueue
{
    const THUMBNAIL_WIDTH = 100;
    const THUMBNAIL_HEIGTH = 100;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ImageWasCreated $event
     * @return void
     */
    public function handle(ImageWasCreated $event)
    {
        $thumbPath = 'public/'.$event->image->getFilePath('thumb');
        File::makeDirectory($thumbPath, 0755, false, true);

        Image::make('public/'.$event->image->getFilePath('original').$event->image->original_filename, ['width' => self::THUMBNAIL_WIDTH, 'height' => self::THUMBNAIL_HEIGTH])->save($thumbPath.$event->image->original_filename);
    }
}
