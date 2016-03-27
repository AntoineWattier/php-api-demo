<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

    factory(App\User::class, 10)->create()->each(function($u) {
        // Create 0 to 3 images per user
        for ($i = 0; $i < rand(0,3); $i++){
            $u->images()->save(factory(App\Image::class)->make());
        }

    });
//        factory(App\Comment::class, 1)->create();
    }
}
