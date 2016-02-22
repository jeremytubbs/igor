<?php

namespace Jeremytubbs\Igor\Models;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $guarded = ['id'];

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

    /**
     * Get all of the posts assets.
     */
    public function assets()
    {
        return $this->morphToMany('Jeremytubbs\Igor\Models\Asset', 'assetable')->withTimestamps();
    }

    /**
     * Get the user.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
