<?php

namespace Jeremytubbs\Igor\Models;

use Illuminate\Database\Eloquent\Model;

class ContentType extends Model
{
    use \Jeremytubbs\Igor\Traits\SluggerTrait;

    public $timestamps = false;

    protected $guarded = ['id'];

    public function contents()
    {
        return $this->hasMany('Jeremytubbs\Igor\Models\Content');
    }
}
