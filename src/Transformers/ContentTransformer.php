<?php

namespace Jeremytubbs\Igor\Transformers;

use Blade;

class ContentTransformer
{
    public function transform($content)
    {
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

        // transform assets
        $asset_group = null;
        $content_assets = $content->assets->groupBy('asset_source_id');
        foreach ($content_assets as $asset_group_key => $asset_group_value) {
            $asset_transform = null;
            foreach ($asset_group_value as $asset_item_value) {
                $asset_transform[$asset_item_value->type->name] = [
                    'uri' => config('app.url') . $asset_item_value->uri,
                ];
            }
            if (isset($asset_item_value->source->id)) {
                $asset_group[$asset_item_value->source->id] = $asset_transform;
            }
        }
        unset($content->assets);
        $content->assets = $asset_group;

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
