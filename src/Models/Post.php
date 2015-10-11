<?php

namespace Jeremytubbs\Igor\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $guarded = [];

    protected $dates = ['published_at'];

    /**
     * Get all of the tags for the post.
     */
    public function tags()
    {
        return $this->morphToMany('Jeremytubbs\Igor\Models\Tag', 'taggable')->withTimestamps();
    }

    /**
     * Get all of the posts categories.
     */
    public function categories()
    {
        return $this->morphToMany('Jeremytubbs\Igor\Models\Category', 'categorable')->withTimestamps();
    }
}
