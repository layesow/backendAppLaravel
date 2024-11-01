<?php

namespace App\Http\Controllers\admin;
use App\Models\Service;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // services par date de ceration decs
        $services = Service::orderBy('created_at', 'desc')->get();
        return response()->json([
            'status' => true,
            'data' => $services,

        ]);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'slug' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }

        $model = new Service();
        $model->title = $request->title;
        $model->slug =Str::slug($request->title);
        $model->short_desc = $request->short_desc;
        $model->content = $request->content;
        $model->status = $request->status;
        $model->save();

        return response()->json([
            'status' => true,
            'message' => 'Service ajouter avec succés',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        //
    }
}
