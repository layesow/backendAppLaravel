<?php

namespace App\Http\Controllers\admin;
use App\Models\Service;

use App\Models\TempImage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Drivers\Gd\Driver;

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

        //save Temp Image here
        if($request->imageId>0){

            $tempImage = TempImage::find($request->imageId);
            if ($tempImage != null) {
                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $fileName = strtotime('now').$model->id.'.'.$ext;

                //creat small thumbnailhere
                $sourcePath = public_path('uploads/temp/'.$tempImage->name);
                $destPath = public_path('uploads/services/small/'.$fileName);
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sourcePath);
                $image->coverDown(500, 600);
                $image->save($destPath);

                //creat large thumbnailhere
                $destPath = public_path('uploads/services/large/'.$fileName);
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sourcePath);
                $image->scaleDown(1200);
                $image->save($destPath);

                $model->image = $fileName;
                $model->save();



            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Service ajouter avec succés',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $service = Service::find($id);
        $service = Service::find($id);
        if($service == null){
            return response()->json([
                'status' => false,
                'message' => 'Service non trouvé',
                ]);
        };
        return response()->json([
            'status' => true,
            'data' => $service
        ]);
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
    public function update(Request $request, $id)
    {
        $service = Service::find($id);
        if($service == null){
            return response()->json([
                'status' => false,
                'message' => 'Service non trouvé',
                ]);
        };

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'slug' => 'required|unique:services,slug,'.$id.',id',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }


        $service->title = $request->title;
        $service->slug =Str::slug($request->title);
        $service->short_desc = $request->short_desc;
        $service->content = $request->content;
        $service->status = $request->status;
        $service->save();

        //save Temp Image here
        if($request->imageId>0){
            $oldImage = $service->image;

            $tempImage = TempImage::find($request->imageId);
            if ($tempImage != null) {
                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $fileName = strtotime('now').$service->id.'.'.$ext;

                //creat small thumbnailhere
                $sourcePath = public_path('uploads/temp/'.$tempImage->name);
                $destPath = public_path('uploads/services/small/'.$fileName);
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sourcePath);
                $image->coverDown(500, 600);
                $image->save($destPath);

                //creat large thumbnailhere
                $destPath = public_path('uploads/services/large/'.$fileName);
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sourcePath);
                $image->scaleDown(1200);
                $image->save($destPath);

                $service->image = $fileName;
                $service->save();

                //delete old image
                if ($oldImage) {
                    File::delete(public_path('uploads/services/small/' . $oldImage),);
                    File::delete(public_path('uploads/services/large/' . $oldImage),);
                }

            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Service modifier avec succés',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $service = Service::find($id);
        $service = Service::find($id);
        if($service == null){
            return response()->json([
                'status' => false,
                'message' => 'Service non trouvé',
                ]);
        };
        $service->delete();

        return response()->json([
            'status' => true,
            'message' => 'Service supprimé avec succés',
        ]);
    }
}
