<?php

namespace App\Http\Controllers\front;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ServiceController extends Controller
{
    public function index(){
        // services all
        $services = Service::where('status',1)->orderBy('created_at', 'DESC')->get();
        return response()->json([
            'status' => true,
            'data' => $services,

        ]);
    }

    public function latestServices(Request $request){
        $services = Service::where('status',1)
            ->take($request->get('limit'))
            ->orderBy('created_at', 'DESC')->get();
            return response()->json([
                'status' => true,
                'data' => $services,

            ]);
    }
}
