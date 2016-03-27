<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    const IMAGES_DIR = 'images/';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'description'
    ];

    /**
     * Get the user record associated with the image.
     *
     * @return Illuminate\Database\Eloquent\Relations\Relation
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Get the comments associated with the image.
     *
     * @return Illuminate\Database\Eloquent\Relations\Relation
     */
    public function comments()
    {
        return $this->hasMany('App\Comment');
    }

    /**
     * Get the commentators associated with the image.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getCommentators()
    {
        return $users = User::select('users.*')
                                ->distinct()
                                ->join('comments', 'comments.user_id', '=', 'users.id')
                                ->where('comments.image_id', $this->id)
                                ->get();
    }

    /**
     * Get the file path of the image.
     *
     * @param string $size
     * @return string
     */
    public function getFilePath($size = null)
    {
        if(!is_null($size)){
            return self::IMAGES_DIR.explode(".",$this->filename)[0].'/'.$size.'/';
        } else {
            return self::IMAGES_DIR.explode(".",$this->filename)[0].'/';
        }
    }
}
