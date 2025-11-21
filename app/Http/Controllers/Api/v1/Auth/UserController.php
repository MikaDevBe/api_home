<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use App\Models\User;
use App\Models\Profile;

class UserController extends Controller
{
  /**
 * Recuperation info d'un compte utilisateur
 * @param int $userId
 * 
 */
  public function show(Request $request): UserResource
  {      
    $user = $request->user();
    return new UserResource($user->load('profile'));
  }
  /**
 * Recuperation info d'un compte utilisateur
 * @param int $userId
 * 
 */

  public function update(Request $request){
 
    try {
          if($request->editMdp == 1){
              $validated = $request->validate([
                'id' => 'required|numeric',
                'name'     => 'required|string|max:50',
                'firstname'     => 'required|string|max:50',
                'identifiant' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('users')->ignore($request->id)
                ],
                'password' => 'required|string|min:8|confirmed',
                'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
              ]);
            }
            else{
              $validated = $request->validate([
                'id' => 'required|numeric',
                'name'     => 'required|string|max:50',
                'firstname'     => 'required|string|max:50',
                'identifiant' => [
                  'required',
                  'string',
                  'max:50',
                  Rule::unique('users')->ignore($request->id)
              ],
              'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
              ]);
            }
            $id = $validated['id'];
            $user = User::findOrFail($id);
            $user->update([
                'name' => $validated['name'] ?? $user->name,
                'firstname' => $validated['firstname'] ?? $user->firstname,
                'identifiant' => $validated['identifiant'] ?? $user->identifiant,
            ]);

            // Données profil
            $profileData = [
                'mail' => $request->mail,
                'phone' => $request->phone,
                'address' => $request->address,
                'town' => $request->town,
                'postal_code' => $request->postal_code,
                'country' => $request->country,
            ];

             // UPLOAD DE L'IMAGE
            if ($request->hasFile('picture') && $request->file('picture')->isValid()) {
                $file = $request->file('picture');
                
                Log::info('Traitement image:', [
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize()
                ]);

                 // S'assurer que le dossier existe
                if (!Storage::disk('public')->exists('profiles')) {
                    Storage::disk('public')->makeDirectory('profiles');
                }

                // Supprimer l'ancienne image si elle existe
                if ($user->profile->image) {
                    if (Storage::disk('public')->exists('profiles/' . $user->profile->image)) {
                        Storage::disk('public')->delete('profiles/' . $user->profile->image);
                    }
                }

                // Générer un nom unique
                $imageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                
                // Stocker l'image
                $file->storeAs('profiles', $imageName, 'public'); // ← Notez le 'public' ici
                $profileData['image'] = $imageName;
              
            } else {
                Log::info('Aucune image à traiter');
            }

            // Mise à jour du profil
            $user->profile->update($profileData);
             
            return response()->json([
                'status'        => true,
                'message'       => 'Modification effectuée',
            ], 200);
            
        } catch (ValidationException $e) {
            return response()->json([
                'status'        => false,
                'message'       => 'Erreur de validation',
                'errors'        => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status'        => false,
                'message'       => 'Probleme serveur',
                'error_detail' => $e->getMessage(),
            ], 500);
        }
    }
}
