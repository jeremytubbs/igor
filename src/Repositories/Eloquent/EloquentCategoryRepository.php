<?php

namespace Jeremytubbs\Igor\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Jeremytubbs\Igor\Repositories\Eloquent\EloquentBaseRepository;
use Jeremytubbs\Igor\Repositories\Contracts\CategoryRepositoryInterface as CategoryRepository;

class EloquentCategoryRepository extends EloquentBaseRepository implements CategoryRepository
{
	/**
     * Find a id by the given slug
     * @param  string    $slug
     * @return int
     */
    public function findIdBySlug($slug)
    {
        $type = $this->findBySlug($slug);
        return $type->id;
    }
}