@php
    $href = $href ?? url('/');
    $admin = $admin ?? false;
@endphp
<a href="{{ $href }}" class="capitex-brand navbar-brand mb-0">
    <span class="capitex-logo-icon"><i class="bi bi-graph-up-arrow"></i></span>
    <span class="capitex-logo-text {{ $admin ? 'admin' : '' }}">{{ $admin ? 'Capitex Admin' : 'Capitex' }}</span>
</a>
