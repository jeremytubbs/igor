<?php

namespace Jeremytubbs\Igor\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class IgorDestroyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igor:destroy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Destroy removed content.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $files)
    {
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
    	//
    }
}
