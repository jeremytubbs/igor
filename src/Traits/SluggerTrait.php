<?php

namespace Jeremytubbs\Igor\Traits;

use Illuminate\Support\Facades\DB;

trait SluggerTrait {

    public function setSlugAttribute($title)
    {
        $slug = str_slug($title);
        $i = 1;
        if (! isset($this->attributes['slug']) || $this->attributes['slug'] != $slug) {
            $slugs = $this->whereRaw("slug REGEXP '^{$slug}(-[0-9]*)?$'")->lists('slug');
            if (count($slugs) < 1) $this->attributes['slug'] = $slug;

            while(! isset($this->attributes['slug'])) {
                $slugger = "{$slug}-{$i}";
                if (! in_array($slugger, (array)$slugs)) $this->attributes['slug'] = $slugger;
                $i++;
            }
        }
    }
}
