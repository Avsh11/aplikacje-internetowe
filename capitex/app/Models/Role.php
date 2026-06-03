<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Slownik rol w systemie (tabela roles)
// Seed: id=1 name=admin, id=2 name=user
// Relacja: Role hasMany User
// Uzywane przy: sprawdzaniu uprawnien (role_id), badge w panelu admina (with('role'))

class Role extends Model
{
    protected $fillable = ['name'];

    // wszyscy userzy z ta rola (np. ilu adminow w systemie)
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
