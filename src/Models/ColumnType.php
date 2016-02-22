<?php

namespace Jeremytubbs\Igor\Models;

use Illuminate\Database\Eloquent\Model;

class ColumnType extends Model
{
    public $timestamps = false;
    protected $guarded = ['id'];

    public function columns()
    {
        return $this->hasMany('Jeremytubbs\Igor\Models\Column');
    }
}
