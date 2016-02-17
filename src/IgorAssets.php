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

    public function handleImage($asset_path)
    {
        clearstatcache();
        $lastModified = filemtime($asset_path);
        $source = $this->igor->findAssetSource($asset_path);

        if ($source->last_modified != $lastModified) {
            // set public path for image
            $path_parts = explode('/', $source->uri);
            $type = array_slice($path_parts, -4, 1);
            $directory = array_slice($path_parts, -3, 1);
            $filepath = "$type[0]/$directory[0]";

            if (config('igor.assets.resize')) {
                $config = ['image_sizes' => $this->getResizePostAssetTypeCascade($type[0])];
                $command = new ResizeImage($source->uri, $filepath, null, $config);
                $this->dispatch($command);
            }

            if (config('igor.assets.deepzoom')) {
                $command = new MakeTiles($source->uri, null, $filepath);
                $this->dispatch($command);
            }
        }

        return $asset_path;
    }
}
