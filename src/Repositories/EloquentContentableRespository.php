<?php

namespace Jeremytubbs\Igor\Repositories;

use Jeremytubbs\Igor\Models\Content;

class EloquentContentableRespository
{
    public static function create($id, $table, $class)
    {
        $post_attributes = \DB::table($table)->find($id);

        if ($post_attributes->published == 1) {
            $content = Content::firstOrCreate([
                'publishable_id' => $id,
                'publishable_type' => $class,
            ]);
        }
    }

    public static function update($id, $table, $class)
    {
        $post_attributes = \DB::table($table)->find($id);

        if ($post_attributes->published == 1) {
            $content = Content::firstOrCreate([
                'publishable_id' => $id,
                'publishable_type' => $class,
            ]);
        }

        if ($post_attributes->published == 0) {
            $content = Content::where('publishable_id', '=', $id)
                        ->where('publishable_type', '=', $class)
                        ->first();
            if ($content) {
                $content->destroy($content->id);
            }
        }
    }

    public static function delete($post, $table, $class)
    {
        $content = Content::where('publishable_id', '=', $id)
                    ->where('publishable_type', '=', $class)
                    ->first();
        if ($content) {
            $content->destroy($content->id);
        }
    }
}
