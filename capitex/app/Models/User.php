<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

// Model uzytkownika - logowanie (Breeze), rola, portfele, preferencje
// Tabela: users (migracja 0001_01_01_000000_create_users_table + kolumny theme/chart z pozniejszej migracji)
// Relacje: User belongsTo Role | User hasMany Portfolio
// Uzywany przez: Auth::user(), rejestracje, SettingsService, AdminController

#[Fillable(['name', 'email', 'password', 'role_id', 'currency', 'theme', 'default_chart_range', 'avatar_path'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    // Fillable = pola ktore mozna ustawic przez create()/update()/fill() (ochrona mass assignment)
    // Hidden = nigdy nie leca do JSON/array (np. przy return user do frontu)

    // casts() - Laravel sam konwertuje typy przy odczycie/zapisie
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed', // haslo hashowane przy zapisie (nie trzymamy plain text)
        ];
    }

    // role_id -> wiersz w tabeli roles (1 admin, 2 user)
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // jeden user moze miec wiele portfeli (XTB, Binance...)
    public function portfolios()
    {
        return $this->hasMany(Portfolio::class);
    }

    public function avatarUrl(): ?string
    {
        if (! $this->avatar_path) {
            return null;
        }

        if (! Storage::disk('public')->exists($this->avatar_path)) {
            return null;
        }

        // Sciezka wzgledna – dziala niezaleznie od APP_URL i portu (127.0.0.1:8000 vs localhost)
        return '/storage/' . ltrim($this->avatar_path, '/');
    }
}
