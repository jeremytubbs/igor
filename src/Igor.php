<?php

namespace Jeremytubbs\Igor;

use Jeremytubbs\Igor\IgorAssets;
use Jeremytubbs\VanDeGraaff\Discharge;
use Jeremytubbs\Igor\Repositories\IgorEloquentRepository as IgorRepository;

class Igor
{
    use \Jeremytubbs\Igor\Traits\IgorStaticHelpers;

    protected $path;

    /**
     * @param string $path
     */
    public function __construct($path, IgorRepository $igor)
    {
        $this->igor = $igor;
        $this->setPaths($path);
        $this->setDischarger($this->index_path);
        $this->setFrontmatter();
        $this->setId();
        $this->setPost();
        $this->updateId();
    }

    public function reAnimate()
    {
        //ar_dump($this->frontmatter);
        // check if published_at is part of frontmatter if published is true
        if (! isset($this->frontmatter['published_at']) && $this->frontmatter['published']) {
            $this->frontmatter = $this->prependToFrontmatter($this->frontmatter, 'published_at', date('Y-m-d H:i:s'));
        }

        // get last modified unixtime from file
        $lastModified = filemtime($this->index_path);

        // check if file has been modified since last save
        if ($this->post->last_modified != $lastModified) {
            $this->igor->updatePost($this->post, $this->path, $this->discharger);
            $this->igor->updatePostCustomFields($this->post, $this->post_type, $this->discharger);

            // save categories
            if (isset($this->frontmatter['categories'])) {
                $this->igor->updatePostCategories($this->post, $this->frontmatter['categories']);
            }

            // save tags
            if (isset($this->frontmatter['tags'])) {
                $this->igor->updatePostTags($this->post, $this->frontmatter['tags']);
            }
        }
    }

    public function reAnimateAssets() {
        $assets_files = $this->getAssetSources($this->images_path);
        $assets_frontmatter = isset($this->frontmatter['assets']) ? $this->frontmatter['assets'] : [];
        $assets_database = $this->igor->getPostDatabaseAssetSources($this->post_model, $this->id, $assets_files);
        if ($assets_files !== null) {
            $this->igor->createOrUpdateAssetSources($assets_files, $assets_frontmatter);
            foreach ($assets_files as $asset_file) {
                if (in_array(basename($asset_file), array_keys($assets_frontmatter))) {
                    (new IgorAssets($this->igor))->handleImage($asset_file);
                    $this->igor->setAssetSourceLastModified($asset_file);
                }
            }
        }
        if ($assets_database !== null) {
            foreach ($assets_database as $asset_database) {
                if (! in_array(basename($asset_database), array_keys($assets_frontmatter))) {
                    $this->igor->deleteAssetSource($this->post_model, $this->id, $asset_database);
                }
            }
        }
    }

    public function setPaths($path)
    {
        $this->path = $path;
        $path_parts = pathinfo($path);
        $this->post_type = basename($path_parts['dirname']);
        $this->post_model = ucfirst(str_singular($this->post_type));
        $this->post_directory = $path_parts['basename'];
        $this->index_path = $path.'/index.md';
        $this->images_path = $path.'/images';
    }

    public function setDischarger($file)
    {
        $this->discharger = new Discharge(file_get_contents($file));
    }

    public function setFrontmatter()
    {
        $this->frontmatter = $this->discharger->getFrontmatter();
    }

    public function setId()
    {
        $this->id = isset($this->frontmatter['id']) ? $this->frontmatter['id'] : null;
    }

    public function setPost()
    {
        $this->post = $this->igor->createOrFindPost($this->post_model, $this->id);
    }

    public function updateId()
    {
        $this->id = $this->post->id;
    }
}
