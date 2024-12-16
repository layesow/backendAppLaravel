<?php

namespace App\Http\Controllers\Admin;

use App\Models\Article;
use App\Models\TempImage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Drivers\Gd\Driver;

class ArticleController extends Controller
{
    //recuperation des articles
    public function index(){
        $articles = Article::orderBy('created_at', 'desc')->get();
        return response()->json([
            'status' => true,
            'data' => $articles,

        ]);
    }

    //creation de articles store
    public function store(Request $request){

        $request->merge(['slug' => Str::slug($request->slug)]);

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'slug' => 'required|unique:articles,slug',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }

        $article = new Article();
        $article->title = $request->title;
        $article->slug =Str::slug($request->slug);
        $article->author = $request->author;
        $article->content = $request->content;
        $article->status = $request->status;
        $article->save();

        //save Temp Image here
        if($request->imageId>0){

            $tempImage = TempImage::find($request->imageId);
            if ($tempImage != null) {
                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $fileName = strtotime('now').$article->id.'.'.$ext;

                //creat small thumbnailhere
                $sourcePath = public_path('uploads/temp/'.$tempImage->name);
                $destPath = public_path('uploads/articles/small/'.$fileName);
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sourcePath);
                $image->coverDown(450, 300);
                $image->save($destPath);

                //creat large thumbnailhere
                $destPath = public_path('uploads/articles/large/'.$fileName);
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sourcePath);
                $image->scaleDown(1200);
                $image->save($destPath);

                $article->image = $fileName;
                $article->save();
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Article ajouter avec succés',
        ]);
    }

    public function show($id)
    {
        $article = Article::find($id);

        if($article == null){
            return response()->json([
                'status' => false,
                'message' => 'article non trouvé',
                ]);
        };
        return response()->json([
            'status' => true,
            'data' => $article
        ]);
    }

    public function update(Request $request, $id){

        $article = Article::find($id);
        if($article == null){
            return response()->json([
                'status' => false,
                'message' => 'article non trouvé',
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


        $article->title = $request->title;
        $article->slug =Str::slug($request->slug);
        $article->author = $request->author;
        $article->content = $request->content;
        $article->status = $request->status;
        $article->save();

        //save Temp Image here
        if($request->imageId>0){
            $oldImage = $article->image;

            $tempImage = TempImage::find($request->imageId);
            if ($tempImage != null) {
                $extArray = explode('.', $tempImage->name);
                $ext = last($extArray);

                $fileName = strtotime('now').$article->id.'.'.$ext;

                //creat small thumbnailhere
                $sourcePath = public_path('uploads/temp/'.$tempImage->name);
                $destPath = public_path('uploads/articles/small/'.$fileName);
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sourcePath);
                $image->coverDown(500, 600);
                $image->save($destPath);

                //creat large thumbnailhere
                $destPath = public_path('uploads/articles/large/'.$fileName);
                $manager = new ImageManager(Driver::class);
                $image = $manager->read($sourcePath);
                $image->scaleDown(1200);
                $image->save($destPath);

                $article->image = $fileName;
                $article->save();
            }
            //delete old image
            if ($oldImage) {
                File::delete(public_path('uploads/articles/small/' . $oldImage),);
                File::delete(public_path('uploads/articles/large/' . $oldImage),);
            }
        }
        return response()->json([
            'status' => true,
            'message' => 'article modifier avec succés',
        ]);
    }

    public function destroy($id)
    {
        $article = Article::find($id);
        if($article == null){
            return response()->json([
                'status' => false,
                'message' => 'article non trouvé',
                ]);
        };
        File::delete(public_path('uploads/projects/small/' . $article->image),);
        File::delete(public_path('uploads/projects/large/' . $article->image),);

        $article->delete();

        return response()->json([
            'status' => true,
            'message' => 'article supprimé avec succés',
        ]);
    }
}
