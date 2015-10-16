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
        $this->types = config('igor.types');
        $this->igor = $igor;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $path = ltrim($_SERVER['REQUEST_URI'], '/');
        $model = "App\\" . studly_case(str_singular($path));
        if (in_array($path, $this->types)) {
            $posts = \App::make($model)
                ->with('tags', 'categories')
                ->where('published', '=', true)
                ->get();
            return view('posts.index', compact('posts'));
        }
        abort(404);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $path = ltrim($_SERVER['REQUEST_URI'], '/');
        $path = explode('/', $path);
        $model = "App\\" . studly_case(str_singular($path[0]));
        if (in_array($path[0], $this->types)) {
            $post = \App::make($model)->where('slug', '=', $slug)
                ->with('tags', 'categories')
                ->where('published', '=', true)
                ->firstOrFail();
            return view('posts.show', compact('post'));
        }
        abort(404);
    }
}
