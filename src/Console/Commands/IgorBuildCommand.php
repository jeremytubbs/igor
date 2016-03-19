<?php

namespace Jeremytubbs\Igor\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Illuminate\Console\AppNamespaceDetectorTrait;
use Symfony\Component\Yaml\Yaml;

class IgorBuildCommand extends Command
{
    use AppNamespaceDetectorTrait;
    use \Jeremytubbs\Igor\Traits\IgorConfigHelpers;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igor:build {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build a custom static post type.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $files, Composer $composer)
    {
        parent::__construct();
        $this->files = $files;
        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->name = $this->argument('name');
        $this->files->makeDirectory(base_path('resources/static/'. $this->getContentTypeName()));
        $this->updateStaticConfig();
        $this->makePostConfig();
        $this->updateTypes();
    }

    protected function makePostConfig()
    {
        $name = $this->getContentTypeName();
        $post_config_path = base_path("resources/static/$name/config.yaml");
        $this->files->put($post_config_path, '# Override main config.yaml here.');
    }

    protected function updateStaticConfig()
    {
        $config_path = base_path('resources/static/config.yaml');
        $config = $this->files->get($config_path);
        $config = Yaml::parse($config);
        $config['types'][] = $this->getContentTypeName();
        $config = Yaml::dump($config, 3);
        $this->files->put($config_path, $config);
    }
}
