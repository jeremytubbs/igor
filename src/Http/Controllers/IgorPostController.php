<?php

namespace Jeremytubbs\Igor\Http\Controllers;

use App\Content;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Jeremytubbs\Igor\Models\ContentType;
use Jeremytubbs\Igor\Transformers\ContentTransformer;
use Jeremytubbs\Igor\Repositories\Eloquent\EloquentContentRepository;
use Jeremytubbs\Igor\Repositories\Eloquent\EloquentContentTypeRepository;

class IgorPostController extends Controller
{

    public function __construct(ContentTransformer $transformer)
    {
        $this->content = new EloquentContentRepository(new Content());
        $this->contentType = new EloquentContentTypeRepository(new ContentType());
        $this->transformer = $transformer;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $custom_type_slug = $request->segment(1);
        $content_type = $this->contentType->findIdBySlug($custom_type_slug);
        $posts = $this->content->getByType($content_type);
        $posts = $this->transformer->collection($posts);
        return view('igor::posts.index', compact('posts'));
    }

    /**
     * Display the specified resource.
     *
     * @param  $slug
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $slug)
    {
        $custom_type_slug = $request->segment(1);
        $content_type = $this->contentType->findIdBySlug($custom_type_slug);
        $post = $this->content->findBySlugAndType($slug, $content_type);
        $post = $this->transformer->item($post);
        return view('igor::posts.show', compact('post'));
    }
}
