<?php

namespace Jeremytubbs\Igor\Http\Controllers;

use App\Content;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Jeremytubbs\Igor\Transformers\ContentTransformer;

class IgorPageController extends Controller
{
    public function __construct(ContentTransformer $transformer)
    {
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
        $page = Content::where('slug', '=', $slug)
            ->where('content_type_id', '=', null)
            ->where('published', '=', true)
            ->with('assets', 'assets.type', 'assets.source')
            ->firstOrFail();
        $page = $this->transformer->item($page);
        return view('igor::pages.show', compact('page'));
    }
}
