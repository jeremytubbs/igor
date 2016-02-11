<?php

namespace Jeremytubbs\Igor\Console\Commands;

use Exception;
use Jeremytubbs\Igor\Igor;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Jeremytubbs\Igor\Repositories\IgorEloquentRepository as IgorRepository;

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
    public function __construct(Filesystem $files, IgorRepository $igor)
    {
        $this->igor = $igor;
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
        $this->igor->createAssetTypes();
        $this->info("It's Alive!");
        $types = $this->files->directories($staticPath);
        foreach ($types as $type_path) {
            $type = basename($type_path);
            $posts = $this->files->directories($staticPath.'/'.$type);
            foreach ($posts as $post) {
                $igor = new Igor($post, new IgorRepository);
                $igor->reAnimate();
            }
        }
    }
}
