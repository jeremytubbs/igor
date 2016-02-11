<?php

namespace Jeremytubbs\Igor\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $guarded = ['id'];

    /**
     * Get all of the posts that are assigned this tag.
     */
    public function posts()
    {
        return $this->morphedByMany('App\Post', 'assetable');
    }

    // public function source()
    // {
    //     return $this->hasOne('Jeremytubbs\Igor\Models\Source', 'id', 'source_id');
    // }

    public function type()
    {
        return $this->belongsTo('Jeremytubbs\Igor\Models\AssetType', 'asset_type_id');
    }

}