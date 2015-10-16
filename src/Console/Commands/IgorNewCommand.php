<?php

namespace Jeremytubbs\Igor\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class IgorNewCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igor:new
                            {title : Title for the new post.}
                            {--type=posts : Type for new post default posts}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a new post.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->title = $this->argument('title');
        $this->slug = str_slug($this->argument('title'));
        $this->type = str_plural(str_slug($this->option('type')));
        $this->makePost();
    }

    protected function makePost()
    {
        $path = base_path('resources/static/'.$this->type.'/'.$this->slug);

        if ($this->files->exists($path)) {
            return $this->error($this->name . ' already exists!');
        }

        $this->files->makeDirectory($path);
        $this->files->put($path.'/index.md', $this->compilePostStub());

        $this->info('New '. str_singular($this->type) . ' created: ' . $this->title);
    }

    protected function compilePostStub()
    {
        $stub = $this->files->get(__DIR__ . '/../../stubs/post/index.stub');
        $stub = str_replace('{{title}}', $this->title, $stub);
        $stub = str_replace('{{slug}}', $this->slug, $stub);
        return $stub;
    }
}
