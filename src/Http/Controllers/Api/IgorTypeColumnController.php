<?php

namespace Jeremytubbs\Igor\Http\Controllers\Api;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Jeremytubbs\Igor\Models\ColumnType;
use Jeremytubbs\Igor\Models\ContentType;
use Jeremytubbs\Igor\Repositories\Eloquent\EloquentColumnTypeRepository;
use Jeremytubbs\Igor\Repositories\Eloquent\EloquentContentTypeRepository;

class IgorTypeColumnController extends Controller
{
    public function __construct()
    {
        $this->columnType = new EloquentColumnTypeRepository(new ColumnType());
        $this->contentType = new EloquentContentTypeRepository(new ContentType());
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return $this->columnType->all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store($type_id)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     * @return Response
     */
    public function show($type_id, $is)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($type_id, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($type_id, $id)
    {
        //
    }
}
