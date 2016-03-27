<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'content'
    ];

    /**
     * Get the user record associated with the comment.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Get the image record associated with the comment.
     */
    public function image()
    {
        return $this->belongsTo('App\Image');
    }
}
