<?php

namespace Jeremytubbs\Igor\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Jeremytubbs\Igor\Igor;

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
    public function __construct(Igor $igor)
    {
        $this->igor = $igor;
        $this->types = config('igor.default_type') + config('igor.custom_types');
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
        foreach ($this->types as $directory => $model ) {
            $files = \File::allFiles($staticPath.'/'.$directory);
            foreach ($files as $file) {
                $post = $this->igor->reAnimate($model, $directory, $file);
            }
        }
    }
}
