<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UsersController extends Controller
{
    public function create(Request $request)
    {
        $error = [];

        $request->validate([
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:3',
        ]);

        try {
 
            DB::beginTransaction();

            $user = User::create([
                'nombres'=> $request->nombres,
                'apellidos'=> $request->apellidos,
                'email'=> $request->email,
                'password' => Hash::make($request->password),
                'rol'=> $request->rol,
            ]);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            $error[0] = $e->getMessage();
        }

        $dtUser = User::all();

        return response()->json([
            'status' => true,
            'message' => 'User created successfully ',
            'token' => $user->createToken('TOKEN_PT_BALDECASH')->plainTextToken,
            'dtUser' => $dtUser,
            'error' =>  $error
        ],200);

    }
    public function list(Request $request)
    {
        $user = User::all();

        return response()->json([
            'status' => 200,
            'dtUsers' => $user,
        ],200);

    }
    public function edit($id)
    {
        $user = User::all();

        $user = User::find($id);

        return response()->json([
            'status' => 200,
            'dtUser' => $user,
        ],200);

    }
    public function delete(Request $request)
    {

        $user = User::find($request->id);

        $error = [];

        if ($user) {

            try {
 
                DB::beginTransaction();

                $user->delete();

                DB::commit();

            } catch (\Exception $e) {
                DB::rollback();
                $error[0] = $e->getMessage();
            }
    
            return response()->json([
                'message' => 'Registro eliminado correctamente',
                'error' => $error,
                'status' => 200,
            ], 200);

        } else {
            
            return response()->json([
                'message' => 'El registro no existe',
                'status' => 404,
            ], 404);
        }

    }
    public function editSave(Request $request)
    {

        $error = [];

        $request->validate([
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$request->id,
            'password' => 'required|string|min:3',
        ]);
        
        try {

            DB::beginTransaction();

            User::where('id', $request->id)->update([
                'nombres' => $request->nombres,
                'apellidos' => $request->apellidos,
                'email' => $request->email,
                'rol' => $request->rol,
                'password' => Hash::make($request->password),
            ]);
            
            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                $error = $e->validator->errors()->all();
            } else {
                $error[0] = $e->getMessage();
            }
        }

        $user = User::all();

        return response()->json([
            'status' => 200,
            'dtUsers' => $user,
            'error' => $error,
        ],200);

    }
}
