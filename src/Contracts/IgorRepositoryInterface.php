<?php

namespace Jeremytubbs\Igor\Contracts;

interface IgorRepositoryInterface
{
    public function createOrFindPost($model, $id);
    public function updatePost($post, $path, $discharger);
    public function updatePostCustomFields($post, $type, $discharger);
    public function createOrFindTags($tags);
    public function updatePostTags($post, $tags);
    public function createOrFindCategories($categories);
    public function updatePostCategories($post, $categories);
    public function createAssetTypes();
    public function createOrUpdateAssetSources($assets);
    public function findAssetSource($uri);
    public function deleteAssetSources($source);
    public function findAssetTypeId($type);
    public function createOrFindAssets($assets, $event);
    public function updatePostAssets($data);
}
