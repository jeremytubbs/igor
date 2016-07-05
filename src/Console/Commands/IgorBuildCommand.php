<?php

namespace Jeremytubbs\Igor\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Console\AppNamespaceDetectorTrait;
use Symfony\Component\Yaml\Yaml;

class IgorBuildCommand extends Command
{
    use \Jeremytubbs\Igor\Traits\IgorConfigHelpers;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igor:build
                            {type : Content post type.}
                            {--route=null : Define a custom route for type.}
                            {--columns=null : name|type||name|type}';

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
    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
        $this->config = config('igor');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->setContentType();
        $this->setCustomRoute();
        $this->setCustomColumns();
        $this->path = base_path('resources/static/_'. $this->type);
        if (! $this->files->exists($this->path)) {
            $this->files->makeDirectory($this->path);
            $this->updateTypes();
            $this->makePostConfig();
        }

        if ($this->route !== 'null') {
            $this->updateContentTypeRoutes();
        }

        if ($this->columns) {
            $this->updateCustomColumns();
        }
        $this->saveConfig();
    }

    protected function makePostConfig()
    {
        $post_config_path = "$this->path/config.yaml";
        $this->files->put($post_config_path, '# Override main config.yaml here.');
    }

    protected function saveConfig()
    {
        $config = $this->createConfig($this->setConfig());
        $this->files->put(config_path('igor.php'), $config);
    }

    protected function setConfig()
    {
        $config = var_export($this->config, true);
        $config = preg_replace('#(?:\A|\n)([ ]*)array \(#i', '[', $config); // Starts
        $config = preg_replace('#\n([ ]*)\),#', "\n$1],", $config); // Ends
        $config = preg_replace('#=> \[\n\s+\],\n#', "=> [],\n", $config); // Empties
        if (gettype($this->config) == 'object') { // Deal with object states
            $config = str_replace('__set_state(array(', '__set_state([', $config);
            $config = preg_replace('#\)\)$#', "])", $config);
        } else {
            $config = preg_replace('#\)$#', "]", $config);
        }
        return $config;
    }

    protected function createConfig($config)
    {
        return <<<EOF
<?php

return $config;

EOF;
    }
}
