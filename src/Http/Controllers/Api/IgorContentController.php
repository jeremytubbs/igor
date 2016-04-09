<?php

namespace Jeremytubbs\Igor\Http\Controllers\Api;

use Illuminate\Http\Request;

use File;
use Artisan;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Content;
use Jeremytubbs\Igor\Repositories\Eloquent\EloquentContentRepository;

class IgorContentController extends Controller
{
    public function __construct()
    {
        $this->content = new EloquentContentRepository(new Content());
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return $this->content->getWith(['type']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return File Contents
     */
    public function show($id)
    {
        $content = $this->content->find($id);
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
        $content = $this->content->find($id);
        File::put($content->path.'/index.md', $request->input('index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy($id)
    {
        $content = $this->content->find($id);
        $success = File::deleteDirectory($contents->path);
        if ($success) {
            $this->content->destroy($content);
        }
    }
}
