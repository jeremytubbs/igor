<?php

namespace Jeremytubbs\Igor\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Jeremytubbs\Igor\Repositories\Eloquent\EloquentBaseRepository;
use Jeremytubbs\Igor\Repositories\Contracts\ColumnTypeRepositoryInterface as ColumnTypeRepository;

class EloquentColumnTypeRepository extends EloquentBaseRepository implements ColumnTypeRepository
{
    /**
     * Find a resource by the given name
     *
     * @param  string $name
     * @return object
     */
    public function findByName($name)
    {
        return $this->model->where('name', $name)->first();
    }


    public function findIdByName($name)
    {
        $type = $this->findByName($name);
        return $type ? $type->id : $type;
    }
}