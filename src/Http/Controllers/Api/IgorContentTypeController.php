<?php

namespace Jeremytubbs\Igor\Http\Controllers\Api;

use Illuminate\Http\Request;

use File;
use Artisan;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Jeremytubbs\Igor\Models\ContentType;
use Jeremytubbs\Igor\Repositories\Eloquent\EloquentContentTypeRepository;

class IgorContentTypeController extends Controller
{
    public function __construct()
    {
        $this->contentType = new EloquentContentTypeRepository(new ContentType());
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return $this->contentType->all();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return File Contents
     */
    public function show($id)
    {
        return $this->contentType->find($id);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     */
    public function destroy($id)
    {
        $contentType = $this->contentType->find($id);
        $success = File::deleteDirectory(base_path("resources/static/$content->slug"));
        if ($success) {
            $this->contentType->destroy($contentType);
        }
    }
}
