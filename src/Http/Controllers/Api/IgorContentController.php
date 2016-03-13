<?php

namespace Jeremytubbs\Igor\Http\Controllers\Api;

use Illuminate\Http\Request;

use File;
use Artisan;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Jeremytubbs\Igor\Models\Content;

class IgorContentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return Content::with('type')->get();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return File Contents
     */
    public function show($id)
    {
        $content = Content::find($id);
        return File::get($content->path.'/index.md');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        $post = Artisan::call('igor:new', [
            'title' => $request->input('title'),
            '--type' => $request->input('type')
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function update(Request $request, $id)
    {
        $content = Content::find($id);
        File::put($content->path.'/index.md', $request->input('index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy($id)
    {
        $content = Content::find($id);
        $success = File::deleteDirectory($contents->path);
        if ($success) {
            Content::destroy($id);
        }
    }
}
