<?php

namespace Jeremytubbs\Igor\Console\Commands;

use Exception;
use Jeremytubbs\Igor\Igor;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

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
    public function __construct(Igor $igor, Filesystem $files)
    {
        $this->igor = $igor;
        $this->types = config('igor.default_type') + config('igor.custom_types');
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
        $this->info("It's Alive!");
        foreach ($this->types as $type => $model ) {
            $posts = $this->files->directories($staticPath.'/'.$type);
            foreach ($posts as $post) {
                $directory = basename($post);
                $path = $post.'/index.md';
                $this->igor->reAnimate($model, $type, $directory, $path);
            }
        }
    }
}
