<?php

namespace Jeremytubbs\Igor\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Jeremytubbs\Igor\Repositories\Eloquent\EloquentBaseRepository;
use Jeremytubbs\Igor\Repositories\Contracts\TagRepositoryInterface as TagRepository;

class EloquentTagRepository extends EloquentBaseRepository implements TagRepository
{
    public function findIdBySlug($slug)
    {
        $type = $this->findBySlug($slug);
        return $type->id;
    }
}