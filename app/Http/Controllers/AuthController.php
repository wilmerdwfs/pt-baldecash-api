<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    
    public function login(LoginRequest $request)
    {
        if(!Auth::attempt($request->only(['email','password'])))
        {
            return response()->json([
                'user' => false,
                'status' => 401,
                'message' => 'Las credenciales no coinciden'
            ],401);
        }
        
        $user = Auth::user();
        
        return response()->json([
            'status' => 200,
            'user' => [
                'email'=>$user->email,
                'idUsu'=>$user->id,
                'rol'=>$user->rol,
                'token' => $user->createToken('TOKEN_PT_BALDECASH')->plainTextToken
            ],
            'message' => 'User logged in successfuly',
        ],200);
    }

    public function close(Request $request)
    {
        
        Auth::guard('web')->logout(); // Cierra la sesión del usuario actual
        
        $request->session()->invalidate(); // Invalida la sesión existente
        $request->session()->regenerateToken(); // Genera un nuevo token CSRF

        return response()->json([
            'status' => 200,
            'message' => 'Session cerrada',
        ],200);
    }
}
