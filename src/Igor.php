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
        $content = $discharger->getContent();
        $markdown = $discharger->getMarkdown();

        // get config from staic directories
        $config = $this->getConfig($post_type);

        // check if published_at is part of frontmatter if published is true
        if (! isset($frontmatter['published_at']) && $frontmatter['published']) {
            $published_at = ['published_at' => date('Y-m-d H:i:s')];
            $frontmatter = $published_at + $frontmatter;
        }

        // get last modified unixtime from file
        $lastModified = filemtime($index_path);
        // check if database id has been added to frontmatter output
        $id = isset($frontmatter['id']) ? $frontmatter['id'] : null;
        // get post or create post
        $post = \App::make('\\App\\'.$post_model)->firstOrNew(['id' => $id]);
        // check if file has been modified since last save
        if ($post->last_modified != $lastModified) {
            $post->user_id = isset($frontmatter['name']) ? User::whereName($frontmatter['name'])->firstOrFail()->pluck('id') : null;
            $post->title = $frontmatter['title'];
            $post->slug = isset($frontmatter['slug']) ? $frontmatter['slug'] : str_slug($frontmatter['title']);
            $post->content = $content;
            $post->layout = isset($config['layout']) ? $config['layout'] : null;
            $post->featured = isset($frontmatter['featured']) ? $frontmatter['featured'] : false;
            $post->published = isset($frontmatter['published']) ? $frontmatter['published'] : false;
            $post->published_at = isset($frontmatter['published_at']) ? $frontmatter['published_at'] : null;
            $post->meta_title = isset($frontmatter['meta_title']) ? $frontmatter['meta_title'] : $frontmatter['title'];
            $post->meta_description = isset($frontmatter['meta_description']) ? $frontmatter['meta_description'] : $this->getExcerpt($content, $config['excerpt_separator']);
            $post->path = $path;

            // get custom fields from config
            $custom_fields = null !== config("custom_fields.$post_type") ? config("custom_fields.$post_type") : [];
            foreach ($custom_fields as $field) {
                $post->$field = isset($frontmatter[$field]) ? $frontmatter[$field] : null;
            }
            $post->save();

            // add the slug to frontmatter
            if (! isset($frontmatter['slug']) || $frontmatter['slug'] != $post->slug) {
                $frontmatter = ['slug' => $post->slug] + $frontmatter;
            }

            // regenerate and save static file with id and published_at
            $this->regenerateStatic($post->id, $index_path, $frontmatter, $markdown);
            clearstatcache();
            $post->last_modified = filemtime($index_path);

            $images_path = $path.'/images/';
            // if image is present or images folder has images
            if (isset($frontmatter['image']) && $this->files->exists($images_path.'/'.$frontmatter['image'])) {
                $public_path = $this->handleImage($post_type, $post_directory, $frontmatter['image']);
                $post->image = $public_path;
            }
            $post->save();

            // save categories
            if (isset($frontmatter['categories'])) {
                $categories_ids = $this->createOrFindCategories($frontmatter['categories']);
                $post->categories()->sync($categories_ids);
            }

            // save tags
            if (isset($frontmatter['tags'])) {
                $tag_ids = $this->createOrFindTags($frontmatter['tags']);
                $post->tags()->sync($tag_ids);
            }
        }
    }
}
