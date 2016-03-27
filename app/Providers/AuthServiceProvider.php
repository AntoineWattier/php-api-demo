<?php

namespace App\Providers;

use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Log;
class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('api', function ($request) {
            if ($request->input('email')) {
                return User::where('email', $request->input('email'))->first();
            }
        });

        // If current_user can edit or delete an image
        Gate::define('manage-image', function ($user, $image) {
            return $user->id === $image->user_id;
        });

        // If current_user can edit or delete a comment
        Gate::define('manage-comment', function ($user, $comment) {
            return $user->id === $comment->user_id;
        });
    }
}
