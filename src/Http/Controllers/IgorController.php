<?php

namespace Jeremytubbs\Igor\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Jeremytubbs\Igor\Igor;

class IgorController extends Controller
{

    public function __construct(Igor $igor)
    {
        $this->igor = $igor;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $type = array_search($request->segment(1), config("igor.type_routes"));
        $model = "App\\" . $type;
        $posts = \App::make($model)
            ->with('tags', 'categories')
            ->where('published', '=', true)
            ->get();
        return view('posts.index', compact('posts'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $slug)
    {
        $type = array_search($request->segment(1), config("igor.type_routes"));
        $model = "App\\" . $type;
        $post = \App::make($model)->where('slug', '=', $slug)
            ->with('tags', 'categories')
            ->where('published', '=', true)
            ->firstOrFail();
        return view('posts.show', compact('post'));
    }
}
