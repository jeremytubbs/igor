<?php

namespace Jeremytubbs\Igor;

use Jeremytubbs\Igor\Models\Tag;
use Jeremytubbs\Igor\Models\Category;
use Jeremytubbs\VanDeGraaff\Discharge;
use Jeremytubbs\VanDeGraaff\Generate;
use Intervention\Image\ImageManager;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

abstract class IgorAbstract {

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
        // get global config
        $global_config = Yaml::parse('resources/static/config.yaml');
        // get type config
        $type_config = 'resources/static/'.$type.'/config.yaml';
        if (file_exists($type_config)) {
            $type_config = Yaml::parse($type_config);
            if (count($type_config) >= 1) {
                // replace values in global config with tyyp config
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
        $config = ['id' => $id] + $config;
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

    public function handleImage($id, $type, $directory, $path)
    {
        $config = $this->getConfig($type);
        // load img into memory
        $img = $this->imageManager->make($path);
        // get filename from path
        $filename = pathinfo($path)['filename'];
        // get format from config
        $format = config('igor.image_format');
        // set image public path
        $img_path = public_path('images/'.$type.'/'.$directory);
        // delete directory if it exists
        if ($this->files->isDirectory($img_path)) {
            $this->files->deleteDirectory($img_path);
        }
        // make directory for images
        $this->files->makeDirectory($img_path, 0775, true);
        // get image sizes from config
        $image_sizes = $config['image_sizes'];
        // if 2x create larger sizes
        if (config('igor.image_2x')) {
            foreach ($image_sizes as $type => $size) {
                $height = $size[0];
                $width = $size[1];
                $image_sizes[$type . '_2x'] = [$height * 2, $width * 2];
            }
        }
        // make and save the images
        foreach ($image_sizes as $type => $size) {
            $height = $size[0];
            $width = $size[1];
            $temp = clone $img;
            // prevent possible upsizing
            $temp->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $temp->encode($format);
            $temp_path = $img_path . '/' . $filename .'_' . $type . '.' . $format;
            $this->files->put($temp_path, $temp);
        }
        return $img_path . '/' . $filename;
    }
}