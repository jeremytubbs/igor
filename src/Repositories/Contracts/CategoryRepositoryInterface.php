<?php

namespace Jeremytubbs\Igor\Repositories\Contracts;

use Jeremytubbs\Igor\Repositories\Contracts\BaseRepositoryInterface as BaseRepository;

interface CategoryRepositoryInterface extends BaseRepository
{
    /**
     * Find a id by the given slug
     * @param  string    $slug
     * @return int
     */
    public function findIdBySlug($slug);
}