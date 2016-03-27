<?php

namespace App\Providers;

use Cache;
use Log;
use Validator;
use App\Comment;
use App\Image;
use App\User;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */

    public function boot()
    {
        /**
         * When a comment is created send a notification to:
         * - Image owner
         * - Image commentators except current user
         */
        Comment::created(function ($comment) {

            // Retrieve image's commentators
            $commentators = $comment->image->getCommentators();

            // Add image's owner if he's not already in array
            if(!$commentators->contains($comment->image->user)) {
                $commentators->push($comment->image->user);
            }

            foreach($commentators as $commentator) {
                // Don't send notification to user who commented
                if($commentator->id != $comment->user_id) {
                    Log::info('Send notification to: ', ['target_id' => $commentator->id]);
                }
            }

            Cache::forget('image_'.$comment->image->id);
        });

        // Cache expiration
        Image::updated(function ($image) {
            Cache::forget('image_'.$image->id);
        });

        Comment::updated(function ($comment) {
            Cache::forget('image_'.$comment->image->id);
        });

        User::updated(function ($user) {
            Cache::forget('user_'.$user->id);
        });
    }


    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
