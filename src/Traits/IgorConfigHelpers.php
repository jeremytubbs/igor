<?php

namespace Jeremytubbs\Igor\Traits;

trait IgorConfigHelpers
{
    protected function updateTypes()
    {
        array_push($this->config['types'], $this->type);
    }

    protected function removeType($type)
    {
        $types = array_flip($this->config['types']);
        unset($types[$type]);
        $types = array_flip($types);
        $this->config['types'] = $types;
    }

    protected function updateContentTypeRoutes()
    {
        $customRoute = [$this->type => $this->route];
        if (isset($this->config['content_type_routes'][$this->type])) {
            unset($this->config['content_type_routes'][$this->type]);
        }
        $this->config['content_type_routes'] += $customRoute;
    }

    protected function updateCustomColumns()
    {
        if (isset($this->config['custom_columns'][$this->type])) {
            unset($this->config['custom_columns'][$this->type]);
        }
        $this->config['custom_columns'] += $this->columns;
    }

    protected function setContentType()
    {
        $this->type = str_plural(snake_case($this->argument('type')));
    }

    protected function setCustomRoute()
    {
        $this->route = $this->option('route');
    }

    protected function setCustomColumns()
    {
        $this->columns = null;
        if ($this->option('columns')) {
            $columns = strpos($this->option('columns'), '||') ? explode('||', $this->option('columns')) : (array) $this->option('columns');
            $this->columns[$this->type] = [];
            foreach ($columns as $column) {
                $column = explode('|', $column);
                $this->columns[$this->type] += [$column[0] => $column[1]];
            }
        }
    }
}