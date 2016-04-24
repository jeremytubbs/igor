<?php

namespace Jeremytubbs\Igor\Repositories\Contracts;

use Jeremytubbs\Igor\Repositories\Contracts\BaseRepositoryInterface as BaseRepository;

interface AssetTypeRepositoryInterface extends BaseRepository
{
    /**
     * Find a resource by the given name
     *
     * @param  string $name
     * @return object
     */
    public function findByName($name);
}