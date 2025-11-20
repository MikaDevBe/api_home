<?php

namespace App\Http\Controllers\Api\v1\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\Profile;

class AuthController extends Controller
{
    /**
     * Register a new account.
     */
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'     => 'required|string|max:50',
                'firstname'     => 'required|string|max:50',
                'identifiant'    => 'required|string|max:50|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user = User::create([
                'name'     => $validated['name'],
                'firstname'     => $validated['firstname'],
                'identifiant'    => $validated['identifiant'],
                'password' => Hash::make($validated['password']),
            ]);

             $user->profile()->create([
                'mail' => null,
                'phone' => null,
                'address' => null,
                'image' => null,
                'town' => null,
                'postal_code' => null,
                'country' => null,
              ]);

            return response()->json([
                'status'        => true,
                'message'       => 'Enregistrement effectué',
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'status'        => false,
                'message'       => 'Erreur de validation',
                'errors'        => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Registration Error: ' . $e->getMessage());

            return response()->json([
                'status'        => false,
                'message'       => 'Probleme serveur',
                'error_detail' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Login and return auth token.
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'identifiant'    => 'required',
                'password' => 'required|string',
            ]);

            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'status'        => false,
                    'message'       => 'Identifiant ou mot de passe erronné',
                ], 401);
            }

            $user = Auth::user();
            if ($user->active != 1) {
              // Déconnecter l'utilisateur immédiatement
              Auth::logout();
              
              return response()->json([
                  'status'  => false,
                  'message' => 'Compte non validé',
              ], 403); // 403 Forbidden
            }
            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json([
                'response_code' => 200,
                'status'        => true,
                'message'       => 'Connexion réussie',
                'user'     => [
                    'id'    => $user->id,
                    'identifiant'  => $user->identifiant,
                ],
                'token'       => $token,
                'token_type'  => 'Bearer',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status'        => false,
                'message'       => 'Erreur de validation',
                'errors'        => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erreur d\'authentification: ' . $e->getMessage());

            return response()->json([
                'status'        => false,
                'message'       => 'Probleme serveur',
            ], 500);
        }
    }

     /**
     * Logout user and revoke tokens — protected route.
     */
    public function logOut(Request $request)
    {
        try {
            $user = $request->user();

            if ($user) {
                $user->tokens()->delete();

                return response()->json([
                    'status'        => true,
                    'message'       => 'Vous êtes deconnecté maintenant',
                ], 200);
            }

            return response()->json([
                'status'        => false,
                'message'       => 'Vous n\'êtes pas connecté',
            ], 401);
        } catch (\Exception $e) {
            Log::error('Logout Error: ' . $e->getMessage());

            return response()->json([
                'status'        => false,
                'message'       => 'Problème serveur',
            ], 500);
        }
    }

}
