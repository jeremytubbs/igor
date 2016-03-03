<?php

namespace Jeremytubbs\Igor\Http\Controllers;

use App\Tag;
use App\Content;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Jeremytubbs\Igor\Transformers\ContentTransformer;

class IgorTagController extends Controller
{
    protected $transformer;

    public function __construct(ContentTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tags = Tag::get();
        return view('igor::tags.index', compact('tags'));
    }

    /**
     * Display the specified resource.
     *
     * @param  $slug
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $contents = Content::where('published', '=', true)
            ->with('type', 'columns', 'columns.type', 'tags', 'categories', 'assets', 'assets.source')
            ->whereHas('tags', function ($query) use ($slug) {
                $query->where('slug', '=', $slug);
            })
            ->whereNotNull('content_type_id') // page content type is null
            ->paginate();

        $contents = $this->transformer->collection($contents);
        if (! $contents) return abort(404);
        return view('igor::tags.show', compact('contents'));
    }
}
