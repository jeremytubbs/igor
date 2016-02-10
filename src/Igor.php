<?php

namespace Jeremytubbs\Igor;

use Jeremytubbs\Igor\IgorImage;
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
    }

    public function reAnimate()
    {
        $frontmatter = $this->discharger->getFrontmatter();

        // check if published_at is part of frontmatter if published is true
        if (! isset($frontmatter['published_at']) && $frontmatter['published']) {
            $frontmatter = $this->prependToFrontmatter($frontmatter, 'published_at', date('Y-m-d H:i:s'));
        }

        // get last modified unixtime from file
        $lastModified = filemtime($this->index_path);
        // check if database id has been added to frontmatter output
        $id = isset($frontmatter['id']) ? $frontmatter['id'] : null;
        // get post or create post
        $post = $this->igor->createOrFindPost($this->post_model, $id);
        // check if file has been modified since last save
        if ($post->last_modified != $lastModified) {
            $this->igor->updatePost($post, $this->path, $this->discharger);
            $this->igor->updatePostCustomFields($post, $this->post_type, $this->discharger);

            // if image is present or images folder has images
            if (isset($frontmatter['image']) && file_exists($this->images_path.'/'.$frontmatter['image'])) {
                (new IgorImage)->handle($this->post_type, $this->post_directory, $frontmatter['image']);
            }

            // save categories
            if (isset($frontmatter['categories'])) {
                $categories_ids = $this->igor->createOrFindCategories($frontmatter['categories']);
                $post->categories()->sync($categories_ids);
            }

            // save tags
            if (isset($frontmatter['tags'])) {
                $tag_ids = $this->igor->createOrFindTags($frontmatter['tags']);
                $post->tags()->sync($tag_ids);
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
        $this->images_path = $path.'/images/';
    }

    public function setDischarger($file)
    {
        $this->discharger = new Discharge(file_get_contents($file));
    }
}
