<?php

namespace Jeremytubbs\Igor\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Jeremytubbs\Igor\Repositories\Eloquent\EloquentBaseRepository;
use Jeremytubbs\Igor\Repositories\Contracts\AssetTypeRepositoryInterface as AssetTypeRepository;

class EloquentAssetTypeRepository extends EloquentBaseRepository implements AssetTypeRepository
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
}