<?php

namespace Jeremytubbs\Igor\Http\Controllers;

use App\Content;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Jeremytubbs\Igor\Repositories\IgorEloquentRepository as IgorRepository;

class IgorPostController extends Controller
{

    public function __construct(IgorRepository $igor)
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
        $custom_type_name = array_search($request->segment(1), config("igor.type_routes"));
        $content_type_id = $this->igor->findContentTypeId($custom_type_name);
        $posts = Content::where('content_type_id', '=', $content_type_id)
            ->with('tags', 'categories', 'assets', 'assets.source')
            ->where('published', '=', true)
            ->get();
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
        $custom_type_name = array_search($request->segment(1), config("igor.type_routes"));
        $content_type_id = $this->igor->findContentTypeId($custom_type_name);
        $post = Content::where('slug', '=', $slug)
            ->where('content_type_id', '=', $content_type_id)
            ->with('tags', 'categories', 'assets', 'assets.source')
            ->where('published', '=', 1)
            ->firstOrFail();
        return view('igor::posts.show', compact('post'));
    }
}
