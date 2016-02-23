<?php

namespace Jeremytubbs\Igor\Transformers;

class ContentColumnTransformer
{
    public function transform($content)
    {
        $columns = null;
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
