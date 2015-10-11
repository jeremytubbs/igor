<?php

namespace Jeremytubbs\Igor;

use Jeremytubbs\Igor\IgorAbstract;


class Igor extends IgorAbstract
{
    public function reAnimate($directory, $file)
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
        $post = \Jeremytubbs\Igor\Models\Post::firstOrNew(['id' => $id]);
        // check if file has been modified since last save
        if ($post->last_modified != $lastModified) {
            $post->title = $config['title'];
            $post->slug = $config['slug'];
            $post->content = $content;
            $post->published = isset($config['published']) ? $config['published'] : false;
            $post->featured = isset($config['featured']) ? $config['featured'] : false;
            $post->published_at = isset($config['published_at']) ? $config['published_at'] : null;
            $post->path = $file;
            $post->save();

            $this->regenerateStatic($post->id, $file, $config, $markdown);
            clearstatcache();
            $post->last_modified = filemtime($file);
            $post->save();

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
