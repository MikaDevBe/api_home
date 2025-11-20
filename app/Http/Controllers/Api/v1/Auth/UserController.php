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
    $user = $request->user();
    return new UserResource($user->load('profile'));
  }
}
