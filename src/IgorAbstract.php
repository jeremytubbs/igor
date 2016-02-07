<?php

namespace Jeremytubbs\Igor;

use Jeremytubbs\Igor\Models\Tag;
use Jeremytubbs\Igor\Models\Category;
use Jeremytubbs\VanDeGraaff\Discharge;
use Jeremytubbs\VanDeGraaff\Generate;
use Jeremytubbs\LaravelResizer\Commands\ResizeImage;
use Intervention\Image\ImageManager;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Yaml\Parser;

abstract class IgorAbstract {

    use \Illuminate\Foundation\Bus\DispatchesJobs;

    public function __construct(Filesystem $files)
    {
        $this->imageManager = new ImageManager(['driver' => config('igor.image_driver')]);
        $this->files = $files;
    }

    public function setDischarger($file)
    {
        $file = new Discharge(file_get_contents($file));
        return $file;
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

    public function regenerateStatic($id, $file, $config, $markdown)
    {
        // add post id to config
        $config = $this->prependToFrontmatter($config, 'id', $id);
        $generator = new Generate($config, rtrim($markdown));
        file_put_contents($file, $generator->makeStatic());
    }

    public function createOrFindTags($tags)
    {
        $tag_ids = null;
        foreach($tags as $t) {
            $tag = Tag::firstOrNew(['name' => $t]);
            $tag->slug = str_slug($t);
            $tag->save();
            $tag_ids[] = $tag->id;
        }
        return $tag_ids;
    }

    public function createOrFindCategories($categories)
    {
        $category_ids = null;
        foreach($categories as $c) {
            $category = Category::firstOrNew(['name' => $c]);
            $category->slug = str_slug($c);
            $category->save();
            $category_ids[] = $category->id;
        }
        return $category_ids;
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

    public function handleImage($type, $directory, $image)
    {
        $config = $this->getConfig($type);
        // set static path for image
        $frontmatter_img = base_path("resources/static/$type/$directory/images/$image");
        // set public path for image
        $filepath = "$type/$directory/$image";
        $command = new ResizeImage($frontmatter_img, $filepath, $config);
        $this->dispatch($command);

        return $image;
    }

    public function appendToFrontmatter($frontmatter, $key, $value)
    {
        return $frontmatter + [$key => $value];
    }

    public function prependToFrontmatter($frontmatter, $key, $value)
    {
        return [$key => $value] + $frontmatter;
    }
}