<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class profile extends Model
{
  protected $primaryKey = 'profile_id';
  protected $fillable = [
    'user_id',
    'mail',
    'phone',
    'address',
    'image',
    'town',
    'postal_code',
    'country',
  ];

  public function user(){
    return $this->belongsTo('App\Models\User');
  }
}
