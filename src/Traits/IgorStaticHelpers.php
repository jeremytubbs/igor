<?php

namespace Jeremytubbs\Igor\Traits;

use File;
use Jeremytubbs\VanDeGraaff\Generate;
use Jeremytubbs\VanDeGraaff\Discharge;
use Symfony\Component\Yaml\Parser;

trait IgorStaticHelpers
{
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
        $this->post = $this->content->firstOrCreate(['id' => $this->id]);
    }

    public function updateId()
    {
        $this->id = $this->post->id;
    }

    public function regenerateStatic($id, $file, $config, $markdown)
    {
        // add post id to config
        $config = $this->prependToFrontmatter($config, 'id', $id);
        $generator = new Generate($config, rtrim($markdown));
        File::put($file, $generator->makeStatic());
    }

    public function getConfig($type)
    {
        $yaml = new Parser();
        // get global config
        $global_config = $yaml->parse(file_get_contents(base_path('resources/static/config.yaml')));
        // get type config
        $type_config_path = base_path('resources/static/'.$type.'/config.yaml');

        if (file_exists($type_config_path)) {
            $type_config = $yaml->parse(file_get_contents($type_config_path));
            if (is_array($type_config) && count($type_config) >= 1) {
                // replace values in global config with type config
                // add all config keys
                foreach ($type_config as $key => $value) {
                    $global_config[$key] = $value;
                }
            }
        }
        return $global_config;
    }

    public function getExcerpt($content, $separator)
    {
        $excerpt = null;

        if (strpos($content, $separator)) {
            $excerpt = strstr($content, $separator, true);
            $excerpt = strstr($excerpt, '<p>');
            $excerpt = strip_tags($excerpt);
            $excerpt = substr($excerpt, 0, 155);
            return $excerpt;
        }

        if (strpos($content, '<p>')) {
            $excerpt = strstr($content, '<p>');
            $excerpt = strip_tags($excerpt);
            $excerpt = substr($excerpt, 0, 155);
            return $excerpt;
        }

        return $excerpt;
    }

    public function appendToFrontmatter($frontmatter, $key, $value)
    {
        return $frontmatter + [$key => $value];
    }

    public function prependToFrontmatter($frontmatter, $key, $value)
    {
        return [$key => $value] + $frontmatter;
    }

    public function findPostId($post_path)
    {
        $path_parts = explode('/', $post_path);
        $post_type = array_slice($path_parts, -4, 1);
        $post_folder = array_slice($path_parts, -3, 1);
        $post_path = base_path("resources/static/$post_type[0]/$post_folder[0]/index.md");
        $discharger = new Discharge(file_get_contents($post_path));
        $frontmatter = $discharger->getFrontmatter();
        return $frontmatter['id'];
    }

    public function findPostModel($post_path)
    {
        $path_parts = explode('/', $post_path);
        $post_type = array_slice($path_parts, -4, 1);
        return ucfirst(str_singular($post_type[0]));
    }

    public function findContentTypeName($post_path)
    {
        $path_parts = explode('/', $post_path);
        $post_type = array_slice($path_parts, -2, 1);
        return $post_type[0];
    }

    public function getAssetSources($image_path)
    {
        $files = File::allFiles($image_path);
        return count($files) > 0 ? $files : null;
    }
}
