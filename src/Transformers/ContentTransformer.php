<?php

namespace Jeremytubbs\Igor\Transformers;

use Blade;

class ContentTransformer
{
    public function transform($content)
    {
        $content->body = Blade::compileString($content->body);

        if (! empty($content->columns)) {
            $content_columns = $content->columns;
            foreach ($content_columns as $column) {
                $type = $column->type->type;
                $name = $column->type->name;
                $content->$name = $column->$type;
            }
        }

        unset($content->columns);

        return $content;
    }

    public function collection($contents)
    {
        $data = null;

        foreach ($contents as $content) {
            $data[] = $this->transform($content);
        }

        return $data;
    }

    public function item($content)
    {
        return $this->transform($content);
    }
}
