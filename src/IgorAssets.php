<?php

namespace Jeremytubbs\Igor;

use Jeremytubbs\Igor\Models\AssetSource;
use Jeremytubbs\LaravelResizer\Commands\ResizeImage;
use Jeremytubbs\LaravelDeepzoom\Commands\MakeTiles;
use Jeremytubbs\Igor\Repositories\IgorEloquentRepository as IgorRepository;

class IgorAssets {

    use \Illuminate\Foundation\Bus\DispatchesJobs;
    use \Jeremytubbs\Igor\Traits\IgorAssetHelpers;

    /**
     * @param string $path
     */
    public function __construct(IgorRepository $igor)
    {
        $this->igor = $igor;
    }

    public function handleImage($type, $directory, $image)
    {
        //static path for image
        $frontmatter_img = base_path("resources/static/$type/$directory/images/$image");
        clearstatcache();
        $lastModified = filemtime($frontmatter_img);
        $source = $this->igor->findAssetSource($frontmatter_img);

        if (!$source || ($source->last_modified != $lastModified)) {
            // set public path for image
            $filepath = "$type/$directory";

            if (config('igor.assets.resize')) {
                $config = ['image_sizes' => $this->getResizePostAssetTypeCascade($type)];
                $command = new ResizeImage($frontmatter_img, $filepath, null, $config);
                $this->dispatch($command);
            }

            if (config('igor.assets.deepzoom')) {
                $command = new MakeTiles($frontmatter_img, null, $filepath);
                $this->dispatch($command);
            }
        }

        return $image;
    }
}
