<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{
    public function authenticate(Request $request){
        //aplique la validation
        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // fait une condition validator
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
        // si la validation est ok
        else {
            // faire la requete
            $credentials = [
                'email' => $request->email,
                'password' => $request->password
            ];
            // si la requete est ok
            if (Auth::attempt($credentials)) {
                //user find auht
                $user = User::find(Auth::user()->id);
                // token
                $token = $user->createToken('auth_token')->plainTextToken;
                return response()->json([
                    'status' => true,
                    'token' => $token,
                    'id' => Auth::user()->id
                ]);
            }
            // faire la requete
            else {
                return response()->json([
                    'status' => false,
                    'message' => 'Emeil ou password incorect'
                ]);
            }
        }


    }

    //logout
    public function logout(){
        // faire la requete
        $user = User::find(Auth::user()->id);
        // delete token
        $user->tokens()->delete();
        return response()->json([
            'status' => true,
            'message' => 'Logout succes'
        ]);
    }

}
