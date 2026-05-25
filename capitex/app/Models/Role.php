<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name'];

    // Relacja - jedna rola przypisana jest do wielu userow
    // Dodac info o roli i walucie zeby do User.php potem na Mass Asignment przepuscilo
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
