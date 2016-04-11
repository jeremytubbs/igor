<?php

namespace Jeremytubbs\Igor\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Jeremytubbs\Igor\Repositories\Eloquent\EloquentBaseRepository;
use Jeremytubbs\Igor\Repositories\Contracts\ColumnRepositoryInterface as ColumnRepository;

class EloquentColumnRepository extends EloquentBaseRepository implements ColumnRepository
{
    /**
     * Find a resource with type
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWithType()
    {
        return $this->model->with('type')->get();
    }
}