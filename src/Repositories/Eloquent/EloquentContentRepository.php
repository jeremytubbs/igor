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
     * Find a resource with included relations
     * @param  array $relations
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWith($content, array $relations)
    {
        return $content->with($relations);
    }

    public function getByCategory($category, $take = 15, $orderBy = 'created_at', $sortOrder = 'DESC')
    {
        $query = $this->model->where('published', '=', true)
            ->with('type', 'columns', 'columns.type', 'tags', 'categories')
            ->whereHas('categories', function ($q) use ($category) {
                $q->where('slug', '=', $category);
            })
            ->orderBy($orderBy, $sortOrder)
            ->whereNotNull('content_type_id'); // page content type is null

        if ($take) return $query->paginate($take);
        return $query->get();
    }

    public function getByTag($tag, $take = 15, $orderBy = 'created_at', $sortOrder = 'DESC')
    {
        $query = $this->model->where('published', '=', true)
            ->with('type', 'columns', 'columns.type', 'tags', 'categories')
            ->whereHas('tags', function ($q) use ($tag) {
                $q->where('slug', '=', $tag);
            })
            ->orderBy($orderBy, $sortOrder)
            ->whereNotNull('content_type_id'); // page content type is null

        if ($take) return $query->paginate($take);
        return $query->get();
    }

    public function getByType($type, $take = 15, $orderBy = 'created_at', $sortOrder = 'DESC')
    {
        $query = $this->model->where('published', '=', true)
            ->where('content_type_id', '=', $type)
            ->with('columns', 'columns.type', 'tags', 'categories')
            ->orderBy($orderBy, $sortOrder);
        if ($take) return $query->paginate($take);
        return $query->get();
    }

    public function findBySlugAndType($slug, $type = null)
    {
        return  $this->model->where('published', '=', true)
            ->with('type', 'columns', 'columns.type', 'tags', 'categories')
            ->where('slug', '=', $slug)
            ->where('content_type_id', '=', $type)
            ->first();
    }
}