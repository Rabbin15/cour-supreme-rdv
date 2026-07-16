<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $agent = Agent::where('email', $request->email)->first();

        if (!$agent || !Hash::check($request->password, $agent->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email ou mot de passe incorrect'
            ], 401);
        }

        if (!$agent->est_actif) {
            return response()->json([
                'status' => 'error',
                'message' => 'Votre compte est désactivé'
            ], 403);
        }

        $token = $agent->createToken('agent-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Connexion réussie',
            'data' => [
                'agent' => $agent,
                'token' => $token
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Déconnexion réussie'
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data' => $request->user()->load('structure')
        ]);
    }
}