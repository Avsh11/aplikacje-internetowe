<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Capitex - Edycja uzytkownika</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-dark text-light">

    <div class="container py-4" style="max-width: 600px;">
        <h1 class="h4 text-danger mb-4">Edycja: {{ $user->name }}</h1>

        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PATCH')

            <div class="mb-3">
                <label class="form-label text-muted small">Nazwa</label>
                <input type="text" name="name" class="form-control bg-dark text-light border-secondary"
                       value="{{ old('name', $user->name) }}" required>
                @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label text-muted small">Email</label>
                <input type="email" name="email" class="form-control bg-dark text-light border-secondary"
                       value="{{ old('email', $user->email) }}" required>
                @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label text-muted small">Rola</label>
                @if ($user->id === Auth::id())
                    <input type="hidden" name="role_id" value="1">
                    <input type="text" class="form-control bg-dark text-light border-secondary" value="admin" disabled>
                    <div class="text-muted small mt-1">Nie mozesz zmienic wlasnej roli.</div>
                @else
                    <select name="role_id" class="form-select bg-dark text-light border-secondary">
                        <option value="2" @selected(old('role_id', $user->role_id) == 2)>user</option>
                        <option value="1" @selected(old('role_id', $user->role_id) == 1)>admin</option>
                    </select>
                @endif
            </div>

            <div class="mb-3">
                <label class="form-label text-muted small">Waluta</label>
                <select name="currency" class="form-select bg-dark text-light border-secondary">
                    @foreach (['PLN', 'USD', 'EUR'] as $c)
                        <option value="{{ $c }}" @selected(old('currency', $user->currency) === $c)>{{ $c }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Zapisz</button>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Anuluj</a>
        </form>
    </div>
</body>
</html>
