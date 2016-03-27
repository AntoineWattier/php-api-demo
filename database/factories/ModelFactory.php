<?php

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function ($faker) {
    return [
        'firstname' => $faker->firstname,
        'lastname' => $faker->lastname,
        'email' => strtolower($faker->safeEmail),
        'password' => Crypt::encrypt($faker->password)
    ];
});

$factory->define(App\Image::class, function ($faker) {

    $filename = $faker->md5;
    $originalFilename = $faker->word.'.jpg';
    $destinationPath = 'public/images/'.$filename.'/original';

    File::makeDirectory($destinationPath, 0755, true);
    File::put($destinationPath.'/'.$originalFilename, file_get_contents($faker->imageUrl($width = 640, $height = 480)));

    return [
        'title' => $faker->catchPhrase,
        'filename' => $filename.'.jpg',
        'original_filename' => $originalFilename,
        'filesize' => $faker->randomNumber,
        'mime' => 'image/jpeg',
        'user_id' => function () {
            return factory(App\User::class)->create()->id;
        }
    ];
});

$factory->define(App\Comment::class, function ($faker) {
    return [
        'content' => $faker->realText($maxNbChars = 100),
        'user_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'image_id' => function () {
            return factory(App\Image::class)->create()->id;
        }
    ];
});

