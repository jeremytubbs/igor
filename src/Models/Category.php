<?php

namespace Jeremytubbs\Igor\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = ['id'];

    /**
     * Get all of the posts that are assigned this tag.
     */
    public function posts()
    {
        return $this->morphedByMany('App\Post', 'categorable');
    }

    /**
     * Get all of the projects that are assigned this tag.
     */
    public function projects()
    {
        return $this->morphedByMany('App\Project', 'categorable');
    }
}
