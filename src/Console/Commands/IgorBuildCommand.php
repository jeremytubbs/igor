<?php

namespace Jeremytubbs\Igor\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Composer;
use Illuminate\Console\AppNamespaceDetectorTrait;
use Symfony\Component\Yaml\Yaml;

class IgorBuildCommand extends Command
{
    use AppNamespaceDetectorTrait;

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
        $this->makeModel();
        $this->makeMigration();
        $this->files->makeDirectory(base_path('resources/static/'. $this->getMigrationName()));
        $this->updateStaticConfig();
    }

    /**
     * Generate the desired migration.
     */
    protected function makeMigration()
    {
        $path = $this->getMigrationPath($this->getMigrationName());

        if ($this->files->exists($path)) {
            return $this->error($this->name . ' already exists!');
        }

        $this->files->put($path, $this->compilePostMigrationStub());

        $this->info('Migration created successfully.');
        $this->composer->dumpAutoloads();
    }

    /**
     * Generate an Eloquent model.
     */
    protected function makeModel()
    {
        $path = $this->getModelPath($this->getModelName());

        if ($this->files->exists($path)) {
            return $this->error($this->name . ' already exists!');
        }

        $this->files->put($path, $this->compilePostModelStub());

        $this->info('Model created successfully.');
    }

    protected function updateStaticConfig()
    {
        $config_path = base_path('resources/static/config.yaml');
        $config = $this->files->get($config_path);
        $config = Yaml::parse($config);
        $config['types'][] = $this->getMigrationName();
        $config = Yaml::dump($config, 2);
        $this->files->put($config_path, $config);
    }

    protected function getMigrationPath($name)
    {
        return base_path() . '/database/migrations/' . date('Y_m_d_His') . '_create_' . $name . '_table.php';
    }

    protected function getModelPath($name)
    {
        $name = str_replace($this->getAppNamespace(), '', $name);
        return $this->laravel['path'] . '/' . str_replace('\\', '/', $name) . '.php';
    }

    protected function compilePostMigrationStub()
    {
        $stub = $this->files->get(__DIR__ . '/../../stubs/postMigration.stub');
        $className = ucwords(str_plural(camel_case($this->name)));
        $tableName = $this->getMigrationName();
        $stub = str_replace('{{class}}', $className, $stub);
        $stub = str_replace('{{table}}', $tableName, $stub);
        return $stub;
    }

    protected function compilePostModelStub()
    {
        $stub = $this->files->get(__DIR__ . '/../../stubs/postModel.stub');
        $className = $this->getModelName();
        $namespace = rtrim($this->getAppNamespace(), '\\');
        $stub = str_replace('{{class}}', $className, $stub);
        $stub = str_replace('{{namespace}}', $namespace, $stub);
        return $stub;
    }

    protected function getModelName()
    {
        return ucwords(str_singular(camel_case($this->name)));
    }

    protected function getMigrationName()
    {
        return str_plural(snake_case($this->name));
    }
}
