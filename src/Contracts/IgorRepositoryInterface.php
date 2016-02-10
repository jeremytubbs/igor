<?php

namespace Jeremytubbs\Igor\Contracts;

interface IgorRepositoryInterface
{
    public function createOrFindPost($model, $id);
    public function updatePost($post, $path, $discharger);
    public function updatePostCustomFields($post, $type);
    public function createOrFindTags($tags);
    public function createOrFindCategories($categories);
}
