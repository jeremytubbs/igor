<?php

namespace Jeremytubbs\Igor\Console\Commands;

use Exception;
use App\Content;
use Jeremytubbs\Igor\Igor;
use Jeremytubbs\Igor\IgorAssets;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Jeremytubbs\Igor\Models\AssetType;
use Jeremytubbs\Igor\Models\ContentType;
use Jeremytubbs\Igor\Models\ColumnType;
use Jeremytubbs\Igor\Repositories\Eloquent\EloquentAssetTypeRepository;
use Jeremytubbs\Igor\Repositories\Eloquent\EloquentContentRepository;
use Jeremytubbs\Igor\Repositories\Eloquent\EloquentContentTypeRepository;
use Jeremytubbs\Igor\Repositories\Eloquent\EloquentColumnTypeRepository;

class IgorWatchCommand extends Command
{
    use \Jeremytubbs\Igor\Traits\IgorAssetHelpers;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igor:watch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "It's Alive!";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        $this->assetType = new EloquentAssetTypeRepository(new AssetType());
        $this->content = new EloquentContentRepository(new Content());
        $this->contentType = new EloquentContentTypeRepository(new ContentType());
        $this->columnType = new EloquentColumnTypeRepository(new ColumnType());
        $this->files = $files;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $staticPath = base_path('resources/static');
        if (! file_exists($staticPath)) {
            throw new Exception("No 'resources/static' folder.");
        }
        $this->createAssetTypes();
        $this->createContentTypes();
        $this->createColumnTypes();
        $this->info("It's Alive!");

        $types = $this->files->directories($staticPath);
        foreach ($types as $type_path) {
            $type = basename($type_path);
            $contents = $this->files->directories("$staticPath/$type");
            foreach ($contents as $post) {
                $igor = new Igor($post);
                $igor->reAnimate();
                $igor = new IgorAssets($post);
                $igor->reAnimateAssets();
            }
            $content_type_id = $this->contentType->findIdBySlug($type);
            $contents_database = $this->content->getByAttributes(['content_type_id' => $content_type_id]);
            foreach($contents_database as $content) {
                if (! in_array($content->path, $contents)) {
                    //Todo: also handle delete of assets for database
                    $this->content->destroy($content);
                    $this->info('Delete: '. $content->path);
                }
            }
        }
    }

    public function createAssetTypes()
    {
        $imageSizes = $this->getAllAssetTypes();
        foreach($imageSizes as $type  => $description) {
            $this->assetType->firstOrCreate(['name' => $type]);
        }
    }

    public function createContentTypes()
    {
        $contentTypes = config('igor.types');

        foreach($contentTypes as $type) {
            $content_type = $this->contentType->firstOrCreate([
                'name' => $type,
                'slug' => str_slug($type)
            ]);
        }
    }

    public function createColumnTypes()
    {
        $columnTypes = config('igor.custom_columns');
        foreach($columnTypes as $contentType) {
            foreach ($contentType as $name => $type) {
                $column_type = $this->columnType->firstOrCreate([
                    'name' => $name,
                    'type' => $type,
                ]);
            }
        }
    }
}
