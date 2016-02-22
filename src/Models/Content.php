<?php

namespace Jeremytubbs\Igor\Models;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $guarded = ['id'];

    protected $dates = ['published_at'];

    /**
     * Get all of the tags.
     */
    public function tags()
    {
        return $this->morphToMany('Jeremytubbs\Igor\Models\Tag', 'taggable')->withTimestamps();
    }

    /**
     * Get all of the categories.
     */
    public function categories()
    {
        return $this->morphToMany('Jeremytubbs\Igor\Models\Category', 'categorable')->withTimestamps();
    }

    /**
     * Get all custom columns.
     */
    public function columns()
    {
        return $this->belongsToMany('Jeremytubbs\Igor\Models\Column');
    }

    /**
     * Get content type.
     */
    public function type()
    {
        return $this->belongsTo('Jeremytubbs\Igor\Model\ContentType', 'content_type_id');
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
