<?php

namespace Jeremytubbs\Igor\Http\Controllers;

use App\Content;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Jeremytubbs\Igor\Transformers\ContentTransformer;
use Jeremytubbs\Igor\Repositories\Eloquent\EloquentContentRepository;

class IgorPageController extends Controller
{
    public function __construct(ContentTransformer $transformer)
    {
        $this->content = new EloquentContentRepository(new Content());
        $this->transformer = $transformer;
    }

    /**
     * Display the specified resource.
     *
     * @param  $slug
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $page = $this->content->findBySlugAndType($slug);
        $page = $this->transformer->item($page);
        return view('igor::pages.show', compact('page'));
    }
}
