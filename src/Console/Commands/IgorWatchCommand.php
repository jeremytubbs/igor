<?php

namespace Jeremytubbs\Igor\Console\Commands;

use Exception;
use Jeremytubbs\Igor\Igor;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Jeremytubbs\Igor\Models\Content;
use Jeremytubbs\Igor\Models\ContentType;
use Jeremytubbs\Igor\Models\ColumnType;
use Jeremytubbs\Igor\Repositories\Eloquent\EloquentContentRepository;
use Jeremytubbs\Igor\Repositories\Eloquent\EloquentContentTypeRepository;
use Jeremytubbs\Igor\Repositories\Eloquent\EloquentColumnTypeRepository;

class IgorWatchCommand extends Command
{
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
            }
        }
    }

    public function createContentTypes()
    {
        $contentTypes = config('igor.types');

        foreach($contentTypes as $type) {
            $content_type = $this->contentType->firstOrCreate([
                'name' => $type
            ]);
            if (null !== config("igor.content_type_routes.$type")) {
                $slug = config("igor.content_type_routes.$type");
                $this->contentType->update($content_type, ['slug' => $slug]);
            } else {
                $this->contentType->update($content_type, ['slug' => str_slug($type)]);
            }
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
