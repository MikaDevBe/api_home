<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;

use App\Models\User;
use App\Models\Profile;

class UserController extends Controller
{
  /**
 * Recuperation info d'un compte utilisateur
 * 
 * Récupere tous les infos d'un utilisateur avec le profile correspondant
 *
 * @param int $userId
 * 
 */
  public function show(Request $request): UserResource
{
    // Sanctum a déjà authentifié l'utilisateur via le token
    // $request->user() contient l'utilisateur connecté
    // C'est le MÊME utilisateur que celui du login
    
    $user = $request->user(); // ← User avec id, name, identifiant, etc.
    
    return new UserResource($user->load('profile'));
}
}
