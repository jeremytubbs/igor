<?php

namespace Jeremytubbs\Igor\Models;

use Illuminate\Database\Eloquent\Model;

class ContentType extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    public function contents()
    {
        return $this->hasMany('Jeremytubbs\Igor\Models\Content');
    }
}
