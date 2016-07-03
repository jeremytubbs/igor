<?php

namespace Jeremytubbs\Igor\Http\Controllers;

use App;
use URL;
use App\Content;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Jeremytubbs\Igor\Models\ContentType;
use Jeremytubbs\Igor\Repositories\Eloquent\EloquentContentRepository;
use Jeremytubbs\Igor\Repositories\Eloquent\EloquentContentTypeRepository;

class IgorSitemapController extends Controller
{
    public function __construct()
    {
        $this->content = new EloquentContentRepository(new Content());
        $this->contentType = new EloquentContentTypeRepository(new ContentType());
    }

    /**
     * Display roumen/sitemap
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        // create new sitemap object
        $sitemap = App::make("sitemap");

        // set cache key (string), duration in minutes (Carbon|Datetime|int), turn on/off (boolean)
        // by default cache is disabled
        // $sitemap->setCache('laravel.sitemap', 60);

        // check if there is cached sitemap and build new only if is not
        if (!$sitemap->isCached()) {
            $pages = $this->content->getByType(null, null);
            foreach ($pages as $page) {
                if($page->published == 1) {
                    $sitemap->add(URL::to($page->slug), $page->updated_at, '0.5', 'monthly');
                }
            }

            foreach (config('igor.types') as $type) {
                $content_type = $this->contentType->findByName($type);
                // get all posts from db, with image relations
                $posts = $this->content->getByType($content_type->id, null);

                $sitemap->add(URL::to($content_type->slug), $posts->first()['updated_at'], '0.8', 'weekly', $images);

                // add every post to the sitemap
                foreach ($posts as $post) {
                    if($post->published == 1) {
                        $sitemap->add(URL::to($content_type->slug.'/'.$post->slug), $post->updated_at, '0.8', 'weekly');
                    }
                }
            }
        }
        // show your sitemap (options: 'xml' (default), 'html', 'txt', 'ror-rss', 'ror-rdf')
        return $sitemap->render('xml');

    }
}