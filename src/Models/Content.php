<?php

namespace Jeremytubbs\Igor\Models;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $fillable = ['content_type_id', 'title', 'slug', 'body', 'layout', 'featured', 'published', 'published_at', 'meta_title', 'meta_description', 'path', 'last_modified', 'config'];

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
        return $this->belongsTo('Jeremytubbs\Igor\Models\ContentType', 'content_type_id');
    }

    /**
     * Get the user.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
