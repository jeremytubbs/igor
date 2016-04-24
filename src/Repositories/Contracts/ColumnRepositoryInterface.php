<?php

namespace Jeremytubbs\Igor\Repositories\Contracts;

use Jeremytubbs\Igor\Repositories\Contracts\BaseRepositoryInterface as BaseRepository;

interface ColumnRepositoryInterface extends BaseRepository
{
    /**
     * Find a resource with type
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWithType();
}