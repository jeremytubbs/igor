<?php

namespace Jeremytubbs\Igor\Http\Controllers;

use App\Content;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class IgorPageController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  $slug
     * @return \Illuminate\Http\Response
     */
    public function showPage($slug)
    {
        $page = Content::where('slug', '=', $slug)
            ->where('content_type_id', '=', null)
            ->where('published', '=', true)
            ->firstOrFail();
        return view('igor::pages.show', compact('page'));
    }
}
