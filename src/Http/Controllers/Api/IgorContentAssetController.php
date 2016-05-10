<?php

namespace Jeremytubbs\Igor\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Content;
use App\Asset;
use Jeremytubbs\Igor\Repositories\Eloquent\EloquentAssetRepository;
use Jeremytubbs\Igor\Repositories\Eloquent\EloquentContentRepository;

class IgorContentAssetController extends Controller
{
    public function __construct()
    {
        $this->asset = new EloquentAssetRepository(new Asset());
        $this->content = new EloquentContentRepository(new Content());
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($content_id)
    {
        return Content::with('assets')->find($content_id);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store($content_id)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $type_id
     * @return Response
     */
    public function show($content_id, $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $type_id
     * @return Response
     */
    public function update($content_id, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($content_id, $id)
    {
        //
    }
}
