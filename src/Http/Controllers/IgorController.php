<?php

namespace Jeremytubbs\Igor\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class IgorController extends Controller
{
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
            ->with('tags', 'categories', 'assets')
            ->where('published', '=', true)
            ->get();
        return json_encode($posts);
        return view('igor::posts.index', compact('posts'));
    }

    /**
     * Display the specified resource.
     *
     * @param  $slug
     * @return \Illuminate\Http\Response
     */
    public function showPost(Request $request, $slug)
    {
        $type = array_search($request->segment(1), config("igor.type_routes"));
        $model = "App\\" . $type;
        $post = \App::make($model)->where('slug', '=', $slug)
            ->with('tags', 'categories', 'assets')
            ->where('published', '=', 1)
            ->firstOrFail();
        return view('igor::posts.show', compact('post'));
    }

    /**
     * Display the specified resource.
     *
     * @param  $slug
     * @return \Illuminate\Http\Response
     */
    public function showPage($slug)
    {
        $page = \App::make("App\\Page")->where('slug', '=', $slug)
            ->where('published', '=', true)
            ->firstOrFail();
        return view('igor::pages.show', compact('page'));
    }
}
