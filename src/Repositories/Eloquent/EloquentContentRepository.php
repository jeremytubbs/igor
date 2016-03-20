<?php

namespace Jeremytubbs\Igor\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Jeremytubbs\Igor\Repositories\Eloquent\EloquentBaseRepository;
use Jeremytubbs\Igor\Repositories\Contracts\ContentRepositoryInterface as ContentRepository;

class EloquentContentRepository extends EloquentBaseRepository implements ContentRepository
{
    /**
     * Update a resource
     * @param $content
     * @param  array $data
     * @return object
     */
    public function update($content, $data)
    {
        $content->update($data);
        $content->tags()->sync(array_get($data, 'tags', []));
        $content->categories()->sync(array_get($data, 'categories', []));
        $content->columns()->sync(array_get($data, 'columns', []));
        return $content;
    }

    /**
     * Update a resource
     * @param $content
     * @param  array $data
     * @return object
     */
    public function attachAssets($content, $data)
    {
        $content->assets()->attach(array_get($data, 'assets', []));
        return $content;
    }

    /**
     * Find a resource with included relations
     * @param  array $relations
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWith($content, array $relations)
    {
        return $content->with($relations);
    }

}