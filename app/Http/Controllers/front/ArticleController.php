<?php

namespace App\Http\Controllers\front;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ArticleController extends Controller
{
    public function index(){
        // services all
        $articles = Article::where('status',1)->orderBy('created_at', 'DESC')->get();
        return response()->json([
            'status' => true,
            'data' => $articles,

        ]);
    }

    public function latestArticles(Request $request){
        $articles = Article::where('status',1)
            ->take($request->get('limit'))
            ->orderBy('created_at', 'DESC')->get();
            return response()->json([
                'status' => true,
                'data' => $articles,

            ]);
    }
}
