<?php

namespace Jeremytubbs\Igor\Http\Controllers;

use App\Content;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Jeremytubbs\Igor\Repositories\IgorEloquentRepository as IgorRepository;

class IgorSitemapController extends Controller
{
    public function __construct(IgorRepository $igor)
    {
        $this->igor = $igor;
    }

    /**
     * Display roumen/sitemap
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        // create new sitemap object
        $sitemap = \App::make("sitemap");

        // set cache key (string), duration in minutes (Carbon|Datetime|int), turn on/off (boolean)
        // by default cache is disabled
        // $sitemap->setCache('laravel.sitemap', 60);

        // check if there is cached sitemap and build new only if is not
        if (!$sitemap->isCached()) {

            $pages = Content::with('assets', 'assets.source', 'assets.type')
                ->where('content_type_id', '=', null)
                ->orderBy('created_at', 'desc')
                ->get();
            foreach ($pages as $page) {
                if($page->published == 1) {
                    $images = [];
                    foreach ($page->assets as $asset) {
                        if ($asset->type->name == 'preview') {
                            $images[] = [
                                'url' => $asset->uri,
                                'title' => $asset->source->title,
                                'caption' => $asset->source->caption
                            ];
                        }
                    }
                    $sitemap->add(\URL::to($page->slug), $page->updated_at, '0.5', 'monthly', $images);
                }
            }

            foreach (config('igor.types') as $type) {
                if (config("igor.content_type_routes.$type")) $type = config("igor.content_type_routes.$type");
                $content_type_id = $this->igor->findContentTypeId($type);
                // get all posts from db, with image relations
                 $posts = Content::where('content_type_id', '=', $content_type_id)
                    ->with('assets', 'assets.source', 'assets.type')
                    ->orderBy('created_at', 'desc')
                    ->get();


                // add every post to the sitemap
                foreach ($posts as $post) {
                    if($post->published == 1) {
                        $images = [];
                        foreach ($post->assets as $asset) {
                            if ($asset->type->name == 'preview') {
                                $images[] = [
                                    'url' => $asset->uri,
                                    'title' => $asset->source->title,
                                    'caption' => $asset->source->caption
                                ];
                            }
                        }
                        $sitemap->add(\URL::to($type.'/'.$post->slug), $post->updated_at, '0.8', 'weekly', $images);
                    }
                }
            }
        }
        // show your sitemap (options: 'xml' (default), 'html', 'txt', 'ror-rss', 'ror-rdf')
        return $sitemap->render('xml');

    }
}