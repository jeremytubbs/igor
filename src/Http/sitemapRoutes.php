<?php

Route::get('sitemap', function(){
    // create new sitemap object
    $sitemap = App::make("sitemap");

    // set cache key (string), duration in minutes (Carbon|Datetime|int), turn on/off (boolean)
    // by default cache is disabled
    // $sitemap->setCache('laravel.sitemap', 60);

    // check if there is cached sitemap and build new only if is not
    if (!$sitemap->isCached()) {
        // get all posts from db, with image relations
        $posts = App::make('\\App\\Post')
                ->with('assets', 'assets.source', 'assets.type')
                ->orderBy('created_at', 'desc')
                ->get();

        // add every post to the sitemap
        foreach ($posts as $post) {
            if($post->published == 1) {
                $images = array();
                foreach ($post->assets as $asset) {
                    if ($asset->type->name == 'preview') {
                        $images[] = [
                            'url' => $asset->uri,
                            'title' => $asset->source->title,
                            'caption' => $asset->source->caption
                        ];
                    }
                }

                $sitemap->add(URL::to('posts/'.$post->slug), $post->updated_at, '0.8', 'weekly', $images);
            }
        }
    }
    // show your sitemap (options: 'xml' (default), 'html', 'txt', 'ror-rss', 'ror-rdf')
    return $sitemap->render('xml');
});
