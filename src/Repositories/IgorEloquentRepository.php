<?php

namespace Jeremytubbs\Igor\Repositories;

use App\User;
use App\Tag;
use App\Category;
use App\Asset;
use App\Content;
use Jeremytubbs\Igor\Models\AssetType;
use Jeremytubbs\Igor\Models\AssetSource;
use Jeremytubbs\Igor\Models\ContentType;
use Jeremytubbs\Igor\Models\Column;
use Jeremytubbs\Igor\Models\ColumnType;
use Jeremytubbs\VanDeGraaff\Discharge;
use Jeremytubbs\Igor\Contracts\IgorRepositoryInterface;

class IgorEloquentRepository implements IgorRepositoryInterface
{
    use \Jeremytubbs\Igor\Traits\IgorStaticHelpers;
    use \Jeremytubbs\Igor\Traits\IgorAssetHelpers;

    public function createOrFindPost($model, $id)
    {
        return Content::firstOrCreate(['id' => $id]);
    }

    public function updatePost($post, $path, $discharger)
    {
        $content_type_id = $this->findContentTypeId($this->findContentTypeName($path));
        $frontmatter = $discharger->getFrontmatter();
        $content = $discharger->getContent();
        $markdown = $discharger->getMarkdown();

        $post->user_id = isset($frontmatter['name']) ? User::whereName($frontmatter['name'])->firstOrFail()->pluck('id') : null;
        $post->content_type_id = $content_type_id;
        $post->title = $frontmatter['title'];
        $post->slug = isset($frontmatter['slug']) ? $frontmatter['slug'] : str_slug($frontmatter['title']);
        $post->body = $content;
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
        $custom_columns = (null !== config("igor.custom_columns.$type")) ? config("igor.custom_columns.$type") : [];
        $custom_column_ids = null;
        foreach($custom_columns as $name => $type) {
            if (isset($frontmatter[$name])) {
                $column_type = ColumnType::where('name', '=', $name)->first();
                $column = Column::firstOrCreate([
                    'column_type_id' => $column_type->id,
                    $type => $frontmatter[$name],
                ]);
                $custom_column_ids[] = $column->id;
            }
        }
        if ($custom_column_ids) $post->columns()->sync($custom_column_ids);
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
        $imageSizes = $this->getAllAssetTypes();

        foreach($imageSizes as $type => $description) {
            $asset_type = AssetType::firstOrNew(['name' => $type]);
            $asset_type->save();
        }
    }

    public function createContentTypes()
    {
        $contentTypes = config('igor.types');

        foreach($contentTypes as $type) {
            $content_type = ContentType::firstOrCreate(['name' => $type]);
        }
    }

    public function findContentTypeId($name)
    {
        $content_type = ContentType::where('name', '=', $name)->first();
        var_dump($name);
        return isset($content_type->id) ? $content_type->id : null;
    }

    public function createCustomColumnTypes()
    {
        $columnTypes = config('igor.custom_columns');
        foreach($columnTypes as $contentType) {
            var_dump($columnTypes);
            foreach ($contentType as $name => $type) {
                $column_type = ColumnType::firstOrCreate([
                    'name' => $name,
                    'type' => $type,
                ]);
            }
        }
    }

    /* Begin Asset Repo Area */

    public function createOrUpdateAssetSources($assets, $frontmatter)
    {
        foreach ($assets as $asset) {
            $filename = basename($asset);
            if ($frontmatter) {
                $sequence = array_search($filename, array_keys($frontmatter));
            }
            $asset_source = AssetSource::firstOrNew(['uri' => $asset]);
            $asset_source->sequence = isset($sequence) ? $sequence : 0;
            $asset_source->mimetype = \File::mimeType($asset);
            $asset_source->title = isset($frontmatter[$filename]['title']) ? $frontmatter[$filename]['title'] : null;
            $asset_source->alt = isset($frontmatter[$filename]['alt']) ? $frontmatter[$filename]['alt'] : null;
            $asset_source->caption = isset($frontmatter[$filename]['caption']) ? $frontmatter[$filename]['caption'] : null;
            $asset_source->description = isset($frontmatter[$filename]['desc']) ? $frontmatter[$filename]['desc'] : null;
            $asset_source->geolocation = isset($frontmatter[$filename]['geolocation']) ? $frontmatter[$filename]['geolocation'] : null;
            $asset_source->licence = isset($frontmatter[$filename]['licence']) ? $frontmatter[$filename]['licence'] : null;
            $asset_source->save();
        }
    }

    public function setAssetSourceLastModified($asset)
    {
        $last_modified = filemtime($asset);
        $asset_source = AssetSource::firstOrNew(['uri' => $asset]);
        $asset_source->last_modified = $last_modified;
        $asset_source->save();
    }

    public function removeAssetSourceLastModified($asset)
    {
        $asset_source = AssetSource::firstOrNew(['uri' => $asset]);
        $asset_source->last_modified = 0;
        $asset_source->save();
    }

    public function findAssetSource($uri)
    {
        $source = AssetSource::where('uri', '=', $uri)->first();
        return $source ? $source : null;
    }

    public function deleteAssetSource($model, $id, $uri)
    {
        $source = $this->findAssetSource($uri);
        $assets = Asset::where('asset_source_id', '=', $source->id)->get();
        $post = $this->createOrFindPost($model, $id);
        foreach ($assets as $asset) {
            $post->assets()->detach($asset->id);
            if (\File::isDirectory($asset->uri)) {
                \File::deleteDirectory($asset->uri);
            } else {
                \File::delete($asset->uri);
            }
            Asset::where('id', '=', $asset->id)->delete();
            $this->removeAssetSourceLastModified($uri);
        }
    }

    public function findAssetTypeId($type)
    {
        $asset = AssetType::where('name', $type)->pluck('id');
        return $asset[0];
    }

    public function createOrFindAssets($assets, $source)
    {
        $asset_ids = null;
        foreach($assets as $type => $uri) {
            $asset_path = str_replace(public_path(), '', $uri);
            $asset_type_id = $this->findAssetTypeId($type);
            $asset_source = $this->findAssetSource($source);
            $asset = Asset::firstOrNew(['uri' => $asset_path]);
            $asset->asset_type_id = $asset_type_id;
            $asset->asset_source_id = $asset_source->id;
            $asset->save();
            $asset_ids[] = $asset->id;
        }
        return $asset_ids;
    }

    public function updatePostAssets($data)
    {
        // TODO: why I am getting double slash from deepzoom
        $source = preg_replace('#/+#','/',$data['source']);
        $assets = $data['output'];
        $asset_ids = $this->createOrFindAssets($assets, $source);
        $types = array_keys($assets);
        $id = $this->findPostId($source);
        $model = $this->findPostModel($source);
        $post = $this->createOrFindPost($model, $id);
        $post->assets()->attach($asset_ids);
    }

    public function getPostDatabaseAssetSources($model, $id, $assets)
    {
        $assets_database = null;
        // get assets in database
        $post = Content::with('assets', 'assets.source')
                ->where('id', '=', $id)
                ->first();
        foreach ($post->assets as $asset) {
            $assets_database[] = $asset->source->uri;
        }
        if (is_array($assets_database)) {
            $assets_database = array_unique($assets_database);
        }

        return $assets_database;
    }
}