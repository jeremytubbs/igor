<?php

namespace Jeremytubbs\Igor\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $guarded = ['id'];

    /**
     * Get all of the page assets.
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
