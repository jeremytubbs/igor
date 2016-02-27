<?php

namespace Jeremytubbs\Igor\Traits;

use Symfony\Component\Yaml\Parser;

trait IgorAssetHelpers
{
    use \Jeremytubbs\Resizer\ResizeHelpersTrait;

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

    public function getAllAssetTypes()
    {
        $all_types = [];
        if (config('igor.assets.resize')) {
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
                        $all_types[$key] = $value;
                    }
                }
            }
            $all_types = $this->setImageSizes($all_types, config('resizer.image_2x'));
        }

        if (config('igor.assets.deepzoom')) {
            $all_types['dzi'] = 'xml description for deepzoom';
            $all_types['jsonp'] = 'jsonp description for deepzoom';
            $all_types['_files'] = 'directory to hold files for deepzoom';
        }

        return $all_types;
    }

    public function getResizePostAssetTypeCascade($type)
    {
        $all_types = null;
        $all_types = $this->getResizerAssetTypes();
        $static_types = $this->getStaticAssetTypes();
        if ($static_types) {
            foreach ($static_types as $key => $value) {
                $all_types[$key] = $value;
            }
        }
        $post_types = $this->getPostTypeAssetTypes($type);
        if ($post_types) {
            foreach ($post_types as $key => $value) {
                $all_types[$key] = $value;
            }
        }
        return $all_types;
    }

}
