<?php

namespace Jeremytubbs\Igor\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $guarded = ['id'];

    /**
     * Get all of the posts that are assigned this asset.
     */
    public function posts()
    {
        return $this->morphedByMany('App\Post', 'assetable');
    }

    public function type()
    {
        return $this->belongsTo('Jeremytubbs\Igor\Models\AssetType', 'asset_type_id');
    }

    public function source()
    {
        return $this->belongsTo('Jeremytubbs\Igor\Models\AssetSource', 'asset_source_id');
    }

}