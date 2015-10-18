<?php

namespace Jeremytubbs\Igor\Traits;

use Illuminate\Support\Facades\DB;

trait SluggerTrait {

    public function setSlugAttribute($title)
    {
        $slug = str_slug($title);
        // check that slug is not set or that current slug no the same
        if (! isset($this->attributes['slug']) || $this->attributes['slug'] != $slug) {
            $slugCount = count($this->whereRaw("slug REGEXP '^{$slug}(-[0-9]*)?$'")->get());
            $this->attributes['slug'] = $slugCount > 0 ? "{$slug}-{$slugCount}" : $slug;
        }
    }
}
