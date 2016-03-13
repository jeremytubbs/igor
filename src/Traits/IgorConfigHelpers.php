<?php

namespace Jeremytubbs\Igor\Traits;

trait IgorConfigHelpers
{
    protected function updateConfigTypes()
    {
        $type = $this->getContentTypeName();
        $config = config('igor');
        array_push($config['types'], $type);
        $types = "'".implode("', '", array_values($config['types']))."'";
        $igorConfig = $this->files->get(config_path('igor.php'));
        $igorConfig = preg_replace("/('types' => )\[.*?\]/", "'types' => [$types]", $igorConfig);
        $this->files->put(config_path('igor.php'), $igorConfig);
    }

    protected function getContentTypeName()
    {
        return str_plural(snake_case($this->name));
    }
}