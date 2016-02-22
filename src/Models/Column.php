<?php

namespace Jeremytubbs\Igor\Models;

use Illuminate\Database\Eloquent\Model;

class Column extends Model
{
    protected $guarded = ['id'];

    public function contents()
    {
        return $this->belongsToMany('Jeremytubbs\Igor\Models\Content');
    }

    public function type()
    {
        return $this->belongsTo('Jeremytubbs\Igor\Models\ColumnType', 'column_type_id');
    }
}
