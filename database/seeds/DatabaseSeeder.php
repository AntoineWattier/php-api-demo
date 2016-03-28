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

        factory(App\Image::class, 10)->create()->each(function($image) {
            // Create 0 to 5 comments per image
            for ($i = 0; $i < rand(0,5); $i++){
                factory(App\Comment::class)->create(['image_id' => $image->id]);
            }
        });
    }
}
