<?php

namespace Jeremytubbs\Igor;

use Jeremytubbs\Igor\IgorAbstract;

class Igor extends IgorAbstract
{
    public function reAnimate($model, $type, $directory, $path)
    {
        $index_path = $path.'/index.md';
        // get file as instance of Jeremytubbs/VanDeGraaff/Discharge
        $discharger = $this->setDischarger($index_path);
        // get output from discharger
        $frontmatter = $discharger->getFrontmatter();
        $content = $discharger->getContent();
        $markdown = $discharger->getMarkdown();
        $config = $this->getConfig($type);

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
        $post = \App::make('\\App\\'.$model)->firstOrNew(['id' => $id]);
        // check if file has been modified since last save
        if ($post->last_modified != $lastModified) {
            var_dump($frontmatter['title']);
            $post->title = $frontmatter['title'];
            $post->slug = isset($frontmatter['slug']) ? $frontmatter['slug'] : str_slug($frontmatter['title']);
            $post->content = $content;
            $post->layout = isset($config['layout']) ? $config['layout'] : null;
            $post->published = isset($frontmatter['published']) ? $frontmatter['published'] : false;
            $post->featured = isset($frontmatter['featured']) ? $frontmatter['featured'] : false;
            $post->published_at = isset($frontmatter['published_at']) ? $frontmatter['published_at'] : null;
            $post->meta_title = isset($frontmatter['meta_title']) ? $frontmatter['meta_title'] : $frontmatter['title'];
            $post->meta_description = isset($frontmatter['description']) ? $frontmatter['description'] : null;
            $post->path = $path;

            // get custom fields from config
            $custom_fields = isset($config['custom_fields']) ? $config['custom_fields'] : [];
            foreach ($custom_fields as $field) {
                $post->$field = isset($frontmatter[$field]) ? $frontmatter[$field] : null;
            }
            $post->save();

            // regenerate and save static file with id and published_at
            $this->regenerateStatic($post->id, $index_path, $frontmatter, $markdown);
            clearstatcache();
            $post->last_modified = filemtime($index_path);

            // if image is present
            if (isset($frontmatter['image'])) {
                $image_path = base_path('resources/static/'.$type.'/'.$directory.'/images/'.$frontmatter['image']);
                // if it is a valid path
                if (file_exists($image_path)) {
                    $public_path = $this->handleImage($post->id, $type, $directory, $image_path);
                    $post->image = $public_path;
                }
            }
            $post->save();

            if (isset($frontmatter['categories'])) {
                $categories_ids = $this->createOrFindCategories($frontmatter['categories']);
                $post->categories()->sync($categories_ids);
            }

            if (isset($frontmatter['tags'])) {
                $tag_ids = $this->createOrFindTags($frontmatter['tags']);
                $post->tags()->sync($tag_ids);
            }
        }
    }
}
