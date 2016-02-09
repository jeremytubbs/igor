<?php

namespace Jeremytubbs\Igor;

use Jeremytubbs\LaravelResizer\Commands\ResizeImage;
use Jeremytubbs\Igor\Repositories\IgorEloquentRepository as Igor;

abstract class IgorAbstract {

    use \Illuminate\Foundation\Bus\DispatchesJobs;
    use \Jeremytubbs\Igor\Traits\IgorStaticHelpers;

    public function __construct(Igor $igor)
    {
        $this->igor = $igor;
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
}