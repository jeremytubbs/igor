<?php

namespace Jeremytubbs\Igor\Repositories;

use App\User;
use App\Tag;
use App\Category;
use App\Asset;
use Jeremytubbs\Igor\Models\AssetType;
use Jeremytubbs\VanDeGraaff\Discharge;
use Jeremytubbs\Igor\Contracts\IgorRepositoryInterface;

class IgorEloquentRepository implements IgorRepositoryInterface
{
    use \Jeremytubbs\Igor\Traits\IgorStaticHelpers;
    use \Jeremytubbs\Igor\Traits\IgorAssetHelpers;

    public function createOrFindPost($model, $id)
    {
        return \App::make('\\App\\'.$model)->firstOrCreate(['id' => $id]);
    }

    public function updatePost($post, $path, $discharger)
    {
        $frontmatter = $discharger->getFrontmatter();
        $content = $discharger->getContent();
        $markdown = $discharger->getMarkdown();

        $post->user_id = isset($frontmatter['name']) ? User::whereName($frontmatter['name'])->firstOrFail()->pluck('id') : null;
        $post->title = $frontmatter['title'];
        $post->slug = isset($frontmatter['slug']) ? $frontmatter['slug'] : str_slug($frontmatter['title']);
        $post->content = $content;
        $post->image = isset($frontmatter['image']) ? $frontmatter['image'] : null;
        $post->layout = isset($frontmatter['layout']) ? $frontmatter['layout'] : null;
        $post->featured = isset($frontmatter['featured']) ? $frontmatter['featured'] : false;
        $post->published = isset($frontmatter['published']) ? $frontmatter['published'] : false;
        $post->published_at = isset($frontmatter['published_at']) ? $frontmatter['published_at'] : null;
        $post->meta_title = isset($frontmatter['meta_title']) ? $frontmatter['meta_title'] : $frontmatter['title'];
        $post->meta_description = isset($frontmatter['meta_description']) ? $frontmatter['meta_description'] : $this->getExcerpt($content, config('igor.excerpt_separator'));
        $post->path = $path;

        if (! isset($frontmatter['slug']) || $frontmatter['slug'] != $post->slug) {
            $frontmatter = $this->prependToFrontmatter($frontmatter, 'slug', $post->slug);
        }

        $this->regenerateStatic($post->id, $path.'/index.md', $frontmatter, $markdown);
        clearstatcache();
        $post->last_modified = filemtime($path.'/index.md');

        $post->save();
        return $post;
    }

    public function updatePostCustomFields($post, $type, $discharger)
    {
        $frontmatter = $discharger->getFrontmatter();
        $custom_fields = null !== config("igor.custom_fields.$type") ? config("igor.custom_fields.$type") : [];
        foreach ($custom_fields as $field) {
            $post->$field = isset($frontmatter[$field]) ? $frontmatter[$field] : null;
        }
        $post->save();
        return $post;
    }

    public function createOrFindTags($tags)
    {
        $tag_ids = null;
        foreach($tags as $t) {
            $tag = Tag::firstOrNew(['name' => $t]);
            $tag->slug = str_slug($t);
            $tag->save();
            $tag_ids[] = $tag->id;
        }
        return $tag_ids;
    }

    public function updatePostTags($post, $tags)
    {
        $tag_ids = $this->createOrFindTags($tags);
        $post->tags()->sync($tag_ids);
        return $post;
    }

    public function createOrFindCategories($categories)
    {
        $category_ids = null;
        foreach($categories as $c) {
            $category = Category::firstOrNew(['name' => $c]);
            $category->slug = str_slug($c);
            $category->save();
            $category_ids[] = $category->id;
        }
        return $category_ids;
    }

    public function updatePostCategories($post, $categories)
    {
        $categories_ids = $this->createOrFindCategories($categories);
        $post->categories()->sync($categories_ids);
        return $post;
    }

    public function createAssetTypes()
    {
        // in asset helpers trait
        $imageSizes = $this->setAllAssetTypes();

        foreach($imageSizes as $type => $description) {
            if (is_array($description)) {
                $height = $description[0];
                $width = $description[1];
                $description = $height . ' x ' . $width;
            }
            $asset_type = AssetType::firstOrNew(['name' => $type]);
            $asset_type->description = $description;
            $asset_type->save();
        }
    }

    public function createOrFindAssets($assets)
    {
        //
    }

    public function updatePostAssets($assets)
    {
        //
    }
}