<?php

namespace Jeremytubbs\Igor\Models;

use Illuminate\Database\Eloquent\Model;

class AssetType extends Model
{
    protected $guarded = ['id'];

    public function assets()
    {
        return $this->hasMany('Jeremytubbs\Igor\Models\Asset');
    }
}
