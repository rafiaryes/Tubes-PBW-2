@push('css')
<style>
    /* Full height for centering the content */
    .full-height {
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>
@endpush

<x-auth-layout>
    <div class="d-flex justify-content-center align-items-center full-height">
        <div class="text-center">
            <!-- Logo at the top -->
            <img src="{{ asset('Logo_KantinPintar.jpg') }}" alt="Logo" class="mb-4" style="max-width: 200px;">

            <!-- Conditional content based on login status -->
            @if(auth()->check())
                <!-- If the user is logged in, show the "Back to Dashboard" button -->
                <div>
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg mb-2">Back to Dashboard</a>
                </div>
            @else
                <!-- If the user is not logged in, show the login and register buttons -->
                <div class="d-flex gap-4">
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-secondary btn-lg">Register</a>
                </div>
            @endif
        </div>
    </div>
</x-auth-layout>
