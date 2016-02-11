<?php

namespace Jeremytubbs\Igor\Traits;

use Symfony\Component\Yaml\Parser;

trait IgorAssetHelpers
{
    public function getResizerAssetTypes()
    {
        return null !== config('resizer.image_sizes') ? config('resizer.image_sizes') : null;
    }

    public function getStaticAssetTypes()
    {
        $yaml = new Parser();
        $static_config = $yaml->parse(file_get_contents(base_path('resources/static/config.yaml')));
        return isset($static_config['image_sizes']) ? $static_config['image_sizes'] : null;
    }

    public function getPostTypeAssetTypes($type)
    {
        $type_path = base_path('resources/static/'.$type.'/config.yaml');
        if (file_exists($type_path)) {
            $yaml = new Parser();
            $type_config = $yaml->parse(file_get_contents($type_path));
        }
        return isset($type_config['image_sizes']) ? $type_config['image_sizes'] : null;
    }

    public function setAllAssetTypes()
    {
        $all_types = null;
        if(config('igor.assets.resize')) {
            $all_types = $this->getResizerAssetTypes();
            $static_types = $this->getStaticAssetTypes();
            if ($static_types) {
                foreach ($static_types as $key => $value) {
                    $all_types[$key] = $value;
                }
            }
            foreach(config('igor.types') as $type) {
                $post_types = $this->getPostTypeAssetTypes($type);
                if ($post_types) {
                    foreach ($post_types as $key => $value) {
                        $all_types["$type-$key"] = $value;
                    }
                }
            }
        }
        return $all_types;
    }
}
