<?php

namespace Jeremytubbs\Igor;

use Jeremytubbs\Igor\IgorAbstract;
use App\User;

class Igor extends IgorAbstract
{
    public function reAnimate($path)
    {
        $path_parts = pathinfo($path);

        $post_type = basename($path_parts['dirname']);
        $post_model = ucfirst(str_singular($post_type));
        $post_directory = $path_parts['basename'];
        $index_path = $path.'/index.md';

        // get file as instance of Jeremytubbs/VanDeGraaff/Discharge
        $discharger = $this->setDischarger($index_path);
        // get output from discharger
        $frontmatter = $discharger->getFrontmatter();
        $markdown = $discharger->getMarkdown();

        // get config from staic directories
        $config = $this->getConfig($post_type);

        // check if published_at is part of frontmatter if published is true
        if (! isset($frontmatter['published_at']) && $frontmatter['published']) {
            $frontmatter = $this->prependToFrontmatter($frontmatter, 'published_at', date('Y-m-d H:i:s'));
        }

        // get last modified unixtime from file
        $lastModified = filemtime($index_path);
        // check if database id has been added to frontmatter output
        $id = isset($frontmatter['id']) ? $frontmatter['id'] : null;
        // get post or create post
        $post = $this->igor->createOrFindPost($post_model, $id);
        // check if file has been modified since last save
        if ($post->last_modified != $lastModified) {
            $this->igor->updatePost($post, $path);
            $this->igor->updatePostCustomFields($post, $post_type);

            $images_path = $path.'/images/';
            // if image is present or images folder has images
            if (isset($frontmatter['image']) && file_exists($images_path.'/'.$frontmatter['image'])) {
                $public_path = $this->handleImage($post_type, $post_directory, $frontmatter['image']);
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
}
