<?php

namespace Jeremytubbs\Igor;

use Jeremytubbs\LaravelResizer\Commands\ResizeImage;
use Jeremytubbs\LaravelDeepzoom\Commands\MakeTiles;

class IgorAssets {

    use \Illuminate\Foundation\Bus\DispatchesJobs;
    use \Jeremytubbs\Igor\Traits\IgorStaticHelpers;

    public function handleImage($type, $directory, $image)
    {
        $config = $this->getConfig($type);
        // set static path for image
        $frontmatter_img = base_path("resources/static/$type/$directory/images/$image");
        // set public path for image
        $filepath = "$type/$directory";

        if (config('igor.assets.resize')) {
            $command = new ResizeImage($frontmatter_img, $filepath, null, $config);
            $this->dispatch($command);
        }

        if (config('igor.assets.deepzoom')) {
            $command = new MakeTiles($frontmatter_img, null, $filepath);
            $this->dispatch($command);
        }

        return $image;
    }
}
