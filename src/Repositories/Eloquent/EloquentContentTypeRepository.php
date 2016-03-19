<?php

namespace Jeremytubbs\Igor\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Jeremytubbs\Igor\Repositories\Eloquent\EloquentBaseRepository;
use Jeremytubbs\Igor\Repositories\Contracts\ContentTypeRepositoryInterface as ContentTypeRepository;

class EloquentContentTypeRepository extends EloquentBaseRepository implements ContentTypeRepository
{
    public function findIdBySlug($slug)
    {
        $type = $this->findBySlug($slug);
        return $type ? $type->id : $type;
    }
}