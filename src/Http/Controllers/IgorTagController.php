<?php

namespace Jeremytubbs\Igor\Http\Controllers;

use App\Tag;
use App\Content;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Jeremytubbs\Igor\Transformers\ContentTransformer;
use Jeremytubbs\Igor\Repositories\Eloquent\EloquentTagRepository;
use Jeremytubbs\Igor\Repositories\Eloquent\EloquentContentRepository;

class IgorTagController extends Controller
{
    protected $transformer;

    public function __construct(ContentTransformer $transformer)
    {
        $this->transformer = $transformer;
        $this->tag = new EloquentTagRepository(new Tag());
        $this->content = new EloquentContentRepository(new Content());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tags = $this->tag->all();
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
        $contents = $this->content->getByTag($slug);
        $contents = $this->transformer->collection($contents);
        if (! $contents) return abort(404);
        return view('igor::tags.show', compact('contents'));
    }
}
