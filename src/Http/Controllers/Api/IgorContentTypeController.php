<?php

namespace Jeremytubbs\Igor\Http\Controllers\Api;

use Illuminate\Http\Request;

use File;
use Artisan;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Jeremytubbs\Igor\Models\ContentType;
use Jeremytubbs\Igor\Models\Content;

class IgorContentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return ContentType::all();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return File Contents
     */
    public function show($id)
    {
        return ContentType::where('id', '=', $id)->with('contents')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        $type = Artisan::call('igor:build', [
            'name' => $request->input('name')
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
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy($id)
    {
        $content = Content::find($id);
        $success = File::deleteDirectory(base_path("resources/static/$contents->slug"));
        if ($success) {
            ContentType::destroy($id);
            Content::where('content_type_id', '=', $id)->destroy();
        }
    }
}
