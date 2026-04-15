@auth('admin')
    {{-- User ID for Echo channel auth --}}
    <meta name="auth-user-id" content="{{ auth('admin')->id() }}">

    {{-- Compiled Echo setup for the admin panel --}}
    @vite('resources/js/admin-echo.js')
@endauth
