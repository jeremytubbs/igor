<?php

namespace Jeremytubbs\Igor;

use Jeremytubbs\Igor\IgorAbstract;


class Igor extends IgorAbstract
{
    public function reAnimate($model, $directory, $file)
    {
        // get file as instance of Jeremytubbs/VanDeGraaff/Discharge
        $discharger = $this->setDischarger($file);
        // get output from discharger
        $config = $discharger->getConfig();
        $content = $discharger->getContent();
        $markdown = $discharger->getMarkdown();

        // check if published_at is part of frontmatter if published is true
        if (! isset($config['published_at']) && $config['published']) {
            $published_at = ['published_at' => date('Y-m-d H:i:s')];
            $config = $published_at + $config;
        }

        // get last modified unixtime from file
        $lastModified = filemtime($file);
        // check if database id has been added to frontmatter output
        $id = isset($config['id']) ? $config['id'] : null;
        // get post or create post
        $post = \App::make('\\App\\'.$model)->firstOrNew(['id' => $id]);
        // check if file has been modified since last save
        if ($post->last_modified != $lastModified) {
            $post->title = $config['title'];
            $post->slug = $config['slug'];
            $post->content = $content;
            $post->published = isset($config['published']) ? $config['published'] : false;
            $post->featured = isset($config['featured']) ? $config['featured'] : false;
            $post->published_at = isset($config['published_at']) ? $config['published_at'] : null;
            $post->path = $file;

            // get custom fields from config
            $custom_fields = null !== config("igor.custom_fields.$directory") ? config("igor.custom_fields.$directory") : [];
            foreach ($custom_fields as $field) {
                $post->$field = isset($config[$field]) ? $config[$field] : null;
            }

            $post->save();

            // regenerate and save static file with id and published_at
            $this->regenerateStatic($post->id, $file, $config, $markdown);
            clearstatcache();
            $post->last_modified = filemtime($file);
            $post->save();

            // if (isset($output['categories'])) {
            //     $oldCategories = $post->categories()->lists('name');
            //     foreach ($oldCategories as $category) {
            //         if (! in_array($category, $output['categories'])) {
            //             $post->categories()->where('name', '=', $category)->delete();
            //         }
            //     }
            //     $newCategories = $this->createOrFindCategories($output['categories']);
            //     $post->categories()->saveMany($newCategories);
            // }

            if (isset($config['categories'])) {
                $categories_ids = $this->createOrFindCategories($config['categories']);
                $post->categories()->sync($categories_ids);
            }

            if (isset($config['tags'])) {
                $tag_ids = $this->createOrFindTags($config['tags']);
                $post->tags()->sync($tag_ids);
            }
        }
    }
}
