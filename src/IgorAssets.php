<?php

namespace Jeremytubbs\Igor;

use App\Asset;
use App\Content;
use Jeremytubbs\Igor\Models\AssetType;
use Jeremytubbs\Igor\Models\AssetSource;
use Jeremytubbs\LaravelResizer\Commands\ResizeImage;
use Jeremytubbs\LaravelDeepzoom\Commands\MakeTiles;
use Jeremytubbs\Igor\Repositories\Eloquent\EloquentContentRepository;
use Jeremytubbs\Igor\Repositories\Eloquent\EloquentAssetRepository;
use Jeremytubbs\Igor\Repositories\Eloquent\EloquentAssetTypeRepository;
use Jeremytubbs\Igor\Repositories\Eloquent\EloquentAssetSourceRepository;

class IgorAssets {

    use \Illuminate\Foundation\Bus\DispatchesJobs;
    use \Jeremytubbs\Igor\Traits\IgorAssetHelpers;
    use \Jeremytubbs\Igor\Traits\IgorStaticHelpers;

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        $this->content = new EloquentContentRepository(new Content());
        $this->asset = new EloquentAssetRepository(new Asset());
        $this->assetType = new EloquentAssetTypeRepository(new AssetType());
        $this->assetSource = new EloquentAssetSourceRepository(new AssetSource());
        $this->setPaths($path);
        $this->setDischarger($this->index_path);
        $this->setFrontmatter();
        $this->setId();
        $this->setPost();
    }

    public function reAnimateAssets() {
        $assets_files = $this->getAssetSources($this->images_path);
        $assets_frontmatter = isset($this->frontmatter['assets']) ? $this->frontmatter['assets'] : [];

        if ($assets_files !== null) {
            foreach ($assets_files as $asset_file) {
                $data = $this->prepareAssetSourceData($asset_file, $assets_frontmatter);
                $asset_source = $this->assetSource->firstOrCreate($data);
                if (in_array(basename($asset_file), array_keys($assets_frontmatter))) {
                    $this->handleImage($asset_file, $this->frontmatter);
                    $data['last_modified'] = filemtime($asset_file);
                    $this->assetSource->update($asset_source, $data);
                }
            }
        }
    }

    public function handleImage($asset_path, $frontmatter)
    {
        clearstatcache();
        $lastModified = filemtime($asset_path);
        $source = $this->assetSource->findByAttributes(['uri' => $asset_path]);
        //$source = $this->igor->findAssetSource($asset_path);

        if ($source->last_modified != $lastModified) {
            // set public path for image
            $path_parts = explode('/', $source->uri);
            $type = array_slice($path_parts, -4, 1);
            $directory = array_slice($path_parts, -3, 1);
            $filepath = "$type[0]/$directory[0]";
            $frontmatter_assets = isset($frontmatter['config']['assets']) ? $frontmatter['config']['assets'] : null;
            $asset_config = $this->getAssetActionConfigCascade($type[0], $frontmatter_assets);

            if ($asset_config['resize']) {
                $config = ['image_sizes' => $this->getResizePostAssetTypeCascade($type[0])];
                $command = new ResizeImage($source->uri, $filepath, null, $config);
                $this->dispatch($command);
            }

            if ($asset_config['deepzoom']) {
                $command = new MakeTiles($source->uri, null, $filepath);
                $this->dispatch($command);
            }
        }

        return $asset_path;
    }

    public function handleImageResponseEvent($data)
    {
        $source_uri = preg_replace('#/+#','/',$data['source']);
        $assets = $data['output'];
        $data['assets'] = $this->getAssetIds($assets, $source_uri);
        $this->content->attachAssets($this->post, $data);
    }

    public function prepareAssetSourceData($asset, $frontmatter)
    {
        $filename = basename($asset);
        $sequence = array_search($filename, array_keys($frontmatter));
        if (isset($frontmatter[$filename])) {
            $data = $frontmatter[$filename];
        }
        $data['uri'] = $asset;
        $data['sequence'] = isset($sequence) ? $sequence + 1 : 1;
        $data['mimetype'] = \File::mimeType($asset);
        return $data;
    }

    public function getAssetIds($assets, $source_uri)
    {
        $asset_ids = [];
        foreach ($assets as $type => $uri) {
            $asset_type = $this->assetType->findByName($type);
            $asset_source = $this->assetSource->findByAttributes(['uri' => $source_uri]);
            $asset_model = $this->asset->firstOrCreate([
                'asset_type_id' => $asset_type->id,
                'asset_source_id' => $asset_source->id,
                'uri' => $uri
                ]);
            $asset_ids[] = $asset_model->id;
        }
        return $asset_ids;
    }
}
