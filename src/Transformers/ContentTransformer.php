<?php

namespace Jeremytubbs\Igor\Transformers;

use Blade;

class ContentTransformer
{
    public function transform($content)
    {
        $content_type = isset($content->type) ? $content->type->slug.'/' : '';

        $content->url = config('app.url').'/'.$content_type.$content->slug;

        $content->config = json_decode($content->config);

        $content->body = Blade::compileString($content->body);

        // transform custom columns
        if (! empty($content->columns)) {
            $content_columns = $content->columns;
            foreach ($content_columns as $column) {
                $type = $column->type->type;
                $name = $column->type->name;
                $content->$name = $column->$type;
            }
        }
        unset($content->columns);

        //transform tags
        $tag_group = null;
        foreach ($content->tags as $tag) {
            $tag_group[] = [
                'id' => $tag->id,
                'slug' => $tag->slug,
                'name' => $tag->name
            ];
        }
        unset($content->tags);
        $content->tags = $tag_group;

        //transform categories
        $category_group = null;
        foreach ($content->categories as $category) {
            $category_group[] = [
                'id' => $category->id,
                'slug' => $category->slug,
                'name' => $category->name
            ];
        }
        unset($content->categories);
        $content->categories = $category_group;

        return $content;
    }

    public function collection($contents)
    {
        $data = null;

        foreach ($contents as $content) {
            $data[] = $this->transform($content);
        }
        $contents->data = $data;

        return $contents;
    }

    public function item($content)
    {
        return $this->transform($content);
    }
}
