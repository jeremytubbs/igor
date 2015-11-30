<?php

namespace Jeremytubbs\Igor\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $guarded = ['id'];

    /**
     * Get the user.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
