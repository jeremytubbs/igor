<?php

namespace Jeremytubbs\Igor;

use App\Tag;
use App\Content;
use App\Category;
use Jeremytubbs\Igor\Models\Column;
use Jeremytubbs\Igor\Models\ColumnType;
use Jeremytubbs\Igor\Models\ContentType;
use Jeremytubbs\VanDeGraaff\Discharge;
use Jeremytubbs\Igor\Repositories\Eloquent\EloquentTagRepository;
use Jeremytubbs\Igor\Repositories\Eloquent\EloquentContentRepository;
use Jeremytubbs\Igor\Repositories\Eloquent\EloquentContentTypeRepository;
use Jeremytubbs\Igor\Repositories\Eloquent\EloquentCategoryRepository;
use Jeremytubbs\Igor\Repositories\Eloquent\EloquentColumnRepository;
use Jeremytubbs\Igor\Repositories\Eloquent\EloquentColumnTypeRepository;

class Igor
{
    use \Jeremytubbs\Igor\Traits\IgorStaticHelpers;

    protected $path;

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        $this->category = new EloquentCategoryRepository(new Category());
        $this->column = new EloquentColumnRepository(new Column());
        $this->columnType = new EloquentColumnTypeRepository(new ColumnType());
        $this->content = new EloquentContentRepository(new Content());
        $this->contentType = new EloquentContentTypeRepository(new ContentType());
        $this->tag = new EloquentTagRepository(new Tag());
        $this->setPaths($path);
        $this->setDischarger($this->index_path);
        $this->setFrontmatter();
        $this->setId();
        $this->setPost();
        $this->updateId();
    }

    public function reAnimate()
    {
        // check if published_at is part of frontmatter if published is true
        $this->addOrRemovePublishedAtTimeToFrontmatter();

        // get last modified unixtime from file
        $lastModified = filemtime($this->index_path);

        // check if file has been modified since last save
        if ($this->post->last_modified != $lastModified) {
            $data = $this->prepareContentData();
            $this->post = $this->content->update($this->post, $data);
            $this->addSlugToFrontmatter();
            $this->regenerateStatic($this->post->id, $this->path.'/index.md', $this->frontmatter, $this->discharger->getMarkdown());
            clearstatcache();
            $data['last_modified'] = filemtime($this->path.'/index.md');
            $this->post = $this->content->update($this->post, $data);
        }
    }

    public function prepareContentData()
    {
        $data = $this->frontmatter;
        $content = $this->discharger->getContent();
        $data['body'] = $content;
        $data['slug'] = isset($data['slug']) ? $data['slug'] : str_slug($data['title']);
        $data['content_type_id'] = $this->contentType->findIdByName($this->findContentTypeName($this->path));
        $data['published'] = isset($data['published']) ? $data['published'] : false;
        $data['published_at'] = $data['published'] ? $data['published_at'] : null;
        $data['meta_title'] = isset($data['meta_title']) ? $data['meta_title'] : $data['title'];
        $data['meta_description'] = isset($data['meta_description']) ? $data['meta_description'] : $this->getExcerpt($content, config('igor.excerpt_separator'));
        $data['path'] = $this->path;
        $data['config'] = isset($data['config']) ? json_encode($data['config']) : null;
        $data['columns'] = $this->getCustomColumnIds();
        $data['categories'] = $this->getCategoryIds();
        $data['tags'] = $this->getTagIds();
        return $data;
    }

    public function addSlugToFrontmatter()
    {
        if (! isset($this->frontmatter['slug']) || $this->frontmatter['slug'] != $this->post->slug) {
            $this->frontmatter = $this->prependToFrontmatter($this->frontmatter, 'slug', $this->post->slug);
        }
    }

    public function addOrRemovePublishedAtTimeToFrontmatter()
    {
        if (! isset($this->frontmatter['published_at']) && $this->frontmatter['published']) {
            $this->frontmatter = $this->prependToFrontmatter($this->frontmatter, 'published_at', date('Y-m-d H:i:s'));
        }
        if (isset($this->frontmatter['published_at']) && ! $this->frontmatter['published']) {
            unset($this->frontmatter['published_at']);
        }
    }

    public function getCustomColumnIds()
    {
        $custom_columns = (null !== config("igor.custom_columns.$this->post_type")) ? config("igor.custom_columns.$this->post_type") : [];
        $custom_column_ids = [];
        foreach($custom_columns as $name => $type) {
            if (isset($this->frontmatter[$name])) {
                $column_type_id = $this->columnType->findIdByName($name);
                $column = $this->column->firstOrCreate([
                    'column_type_id' => $column_type_id,
                    $type => $this->frontmatter[$name],
                ]);
                $custom_column_ids[] = $column->id;
            }
        }
        return $custom_column_ids;
    }

    public function getTagIds()
    {
        $tag_ids = [];
        if (isset($this->frontmatter['tags'])) {
            foreach($this->frontmatter['tags'] as $t) {
                $tag = $this->tag->firstOrCreate([
                    'name' => $t,
                    'slug' => str_slug($t)
                ]);
                $tag_ids[] = $tag->id;
            }
        }
        return $tag_ids;
    }

    public function getCategoryIds()
    {
        $category_ids = [];
        if (isset($this->frontmatter['categories'])) {
            foreach($this->frontmatter['categories'] as $c) {
                $category = $this->category->firstOrCreate([
                    'name' => $c,
                    'slug' => str_slug($c)
                ]);
                $category_ids[] = $category->id;
            }
        }
        return $category_ids;
    }
}
