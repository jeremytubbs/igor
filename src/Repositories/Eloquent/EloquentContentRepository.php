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

    public function paginateByCategory($category, $take = 15, $orderBy = 'created_at', $sortOrder = 'DESC')
    {
        return $this->model->where('published', '=', true)
            ->with('type', 'columns', 'columns.type', 'tags', 'categories', 'assets', 'assets.type', 'assets.source')
            ->whereHas('categories', function ($query) use ($category) {
                $query->where('slug', '=', $category);
            })
            ->orderBy($orderBy, $sortOrder)
            ->whereNotNull('content_type_id') // page content type is null
            ->paginate($take);
    }

}