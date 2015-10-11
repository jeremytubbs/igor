<?php

namespace Jeremytubbs\Igor;

use Jeremytubbs\Igor\Models\Tag;
use Jeremytubbs\Igor\Models\Category;
use Jeremytubbs\VanDeGraaff\Discharge;
use Jeremytubbs\VanDeGraaff\Generate;

abstract class IgorAbstract {

    public function setDischarger($file)
    {
        $file = new Discharge(file_get_contents($file));
        return $file;
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

}