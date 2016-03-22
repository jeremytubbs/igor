<?php

namespace Jeremytubbs\Igor\Http\Controllers;

use App\Category;
use App\Content;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Jeremytubbs\Igor\Transformers\ContentTransformer;
use Jeremytubbs\Igor\Repositories\Eloquent\EloquentContentRepository;
use Jeremytubbs\Igor\Repositories\Eloquent\EloquentCategoryRepository;

class IgorCategoryController extends Controller
{
    protected $transformer;

    public function __construct(ContentTransformer $transformer)
    {
        $this->category = new EloquentCategoryRepository(new Category());
        $this->content = new EloquentContentRepository(new Content());
        $this->transformer = $transformer;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = $this->category->all();
        return view('igor::categories.index', compact('categories'));
    }

    /**
     * Display the specified resource.
     *
     * @param  $slug
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $slug)
    {
        // push category into session
        $request->session()->put('category', $slug);

        $contents = $this->content->paginateByCategory($slug);

        $contents = $this->transformer->collection($contents);
        if (! $contents) return abort(404);
        return view('igor::categories.show', compact('contents'));
    }
}
