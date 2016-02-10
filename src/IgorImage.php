<?php

namespace Jeremytubbs\Igor;

use Jeremytubbs\LaravelResizer\Commands\ResizeImage;

class IgorImage {

    use \Illuminate\Foundation\Bus\DispatchesJobs;
    use \Jeremytubbs\Igor\Traits\IgorStaticHelpers;

    public function handle($type, $directory, $image)
    {
        $config = $this->getConfig($type);
        // set static path for image
        $frontmatter_img = base_path("resources/static/$type/$directory/images/$image");
        // set public path for image
        $filepath = "$type/$directory";
        $command = new ResizeImage($frontmatter_img, $filepath, $config);
        $this->dispatch($command);

        return $image;
    }
}
