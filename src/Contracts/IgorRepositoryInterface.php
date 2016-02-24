<?php

namespace Jeremytubbs\Igor\Contracts;

interface IgorRepositoryInterface
{
    public function createOrFindContent($id);
    public function updateContent($post, $path, $discharger);
    public function updateContentCustomColumns($post, $type, $discharger);
    public function createOrFindTags($tags);
    public function updateContentTags($post, $tags);
    public function createOrFindCategories($categories);
    public function updateContentCategories($post, $categories);
    public function createAssetTypes();
    public function createOrUpdateAssetSources($assets, $frontmatter);
    public function setAssetSourceLastModified($asset);
    public function removeAssetSourceLastModified($asset);
    public function findAssetSource($uri);
    public function deleteAssetSource($model, $id, $uri);
    public function findAssetTypeId($type);
    public function createOrFindAssets($assets, $source);
    public function updateContentAssets($data);
    public function getContentDatabaseAssetSources($model, $id, $assets);
}
