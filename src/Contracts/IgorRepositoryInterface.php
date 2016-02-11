<?php

namespace Jeremytubbs\Igor\Contracts;

interface IgorRepositoryInterface
{
    public function createOrFindPost($model, $id);
    public function updatePost($post, $path, $discharger);
    public function updatePostCustomFields($post, $type, $discharger);
    public function createOrFindTags($tags);
    public function updatePostTags($tags);
    public function createOrFindCategories($categories);
    public function updatePostCategories($categories);
    public function createAssetTypes();
    public function createOrFindAssets($assets);
    public function updatePostAssets($assets);
}
