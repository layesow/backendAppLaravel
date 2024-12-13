<?php

namespace App\Http\Controllers\Admin;

use App\Models\Project;
use App\Models\TempImage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Drivers\Gd\Driver;

class ProjectController extends Controller
{
    // methode return all project
    public function index(){
        $projects = Project::orderBy('created_at', 'desc')->get();
        return response()->json([
            'status' => true,
            'data' => $projects,

        ]);
    }

    // methode insert project in db
    public function store(Request $request){

        //dammy title
        $request->merge(['slug' => Str::slug($request->slug)]);

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'slug' => 'required|unique:projects,slug',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }

        $project = new Project();
        $project->title = $request->title;
        $project->slug =Str::slug($request->slug);
        $project->short_desc = $request->short_desc;
        $project->content = $request->content;
        $project->construction_type = $request->construction_type;
        $project->sector = $request->sector;
        $project->location = $request->location;
        $project->status = $request->status;
        $project->save();

        //save Temp Image here
        if($request->imageId>0){

            $tempImage = TempImage::find($request->imageId);
            if ($tempImage != null) {
                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $fileName = strtotime('now').$project->id.'.'.$ext;

                //creat small thumbnailhere
                $sourcePath = public_path('uploads/temp/'.$tempImage->name);
                $destPath = public_path('uploads/projects/small/'.$fileName);
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sourcePath);
                $image->coverDown(500, 600);
                $image->save($destPath);

                //creat large thumbnailhere
                $destPath = public_path('uploads/projects/large/'.$fileName);
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sourcePath);
                $image->scaleDown(1200);
                $image->save($destPath);

                $project->image = $fileName;
                $project->save();
            }
        }
        return response()->json([
            'status' => true,
            'message' => 'Projet ajouter avec succés',
        ]);
    }

    public function show($id)
    {
        $project = Project::find($id);
        $project = Project::find($id);

        if($project == null){
            return response()->json([
                'status' => false,
                'message' => 'Projet non trouvé',
                ]);
        };
        return response()->json([
            'status' => true,
            'data' => $project
        ]);
    }

    public function update(Request $request, $id){

        $project = Project::find($id);
        if($project == null){
            return response()->json([
                'status' => false,
                'message' => 'Projet non trouvé',
                ]);
        };
        //dammy title
        $request->merge(['slug' => Str::slug($request->slug)]);

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'slug' => 'required|unique:projects,slug,'.$id.',id',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }


        $project->title = $request->title;
        $project->slug =Str::slug($request->slug);
        $project->short_desc = $request->short_desc;
        $project->content = $request->content;
        $project->construction_type = $request->construction_type;
        $project->sector = $request->sector;
        $project->location = $request->location;
        $project->status = $request->status;
        $project->save();

        //save Temp Image here
        if($request->imageId>0){
            $oldImage = $project->image;

            $tempImage = TempImage::find($request->imageId);
            if ($tempImage != null) {
                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $fileName = strtotime('now').$project->id.'.'.$ext;

                //creat small thumbnailhere
                $sourcePath = public_path('uploads/temp/'.$tempImage->name);
                $destPath = public_path('uploads/projects/small/'.$fileName);
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sourcePath);
                $image->coverDown(500, 600);
                $image->save($destPath);

                //creat large thumbnailhere
                $destPath = public_path('uploads/projects/large/'.$fileName);
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sourcePath);
                $image->scaleDown(1200);
                $image->save($destPath);

                $project->image = $fileName;
                $project->save();
            }
            //delete old image
            if ($oldImage) {
                File::delete(public_path('uploads/projects/small/' . $oldImage),);
                File::delete(public_path('uploads/projects/large/' . $oldImage),);
            }
        }
        return response()->json([
            'status' => true,
            'message' => 'Projet modifier avec succés',
        ]);
    }

    public function destroy($id)
    {
        $project = Project::find($id);
        if($project == null){
            return response()->json([
                'status' => false,
                'message' => 'Project non trouvé',
                ]);
        };
        File::delete(public_path('uploads/projects/small/' . $project->image),);
        File::delete(public_path('uploads/projects/large/' . $project->image),);

        $project->delete();

        return response()->json([
            'status' => true,
            'message' => 'Project supprimé avec succés',
        ]);
    }
}
