<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - SOMS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" 
    rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@else
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
@endif
    <style>
        body {
            font-family: 'Nunito', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white px-4 py-3 rounded-xl shadow-lg w-full max-w-sm">
        <div>
            <p class="text-center text-gray-500 capitalize my-2">Enter your new password</p>
        </div>

        @if ($errors->any())
            <div class="text-red-500 text-sm text-center">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="post" class="space-y-2">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <!-- Email (Auto-filled and Read-only) -->
            <div>
                <label for="Email">Email:</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-envelope text-green-600" aria-hidden="true"></i>
                    </div>
                    <input type="email" id="email" name="email" value="{{ old('email', request()->email) }}"
                        placeholder="Email" aria-label="Email"
                        class="block w-full pl-10 pr-3 py-2 border border-green-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm  cursor-not-allowed bg-gray-200"
                        required readonly autofocus autocomplete="off">
                </div>
            </div>

            <div>
                <label for="password">New Password:</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-green-600" aria-hidden="true"></i>
                    </div>

                    <input type="password" id="password" name="password" value="<?= htmlspecialchars('') ?>"
                        placeholder="Password" aria-label="Password"
                        class="block w-full pl-10 pr-3 py-2 border border-green-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
                        required autofocus autocomplete="new-password">
                </div>

                <!-- Confirm Password -->
                <div class="">
                    <label for="Confirm-Password">Confirm New Password:</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-green-600" aria-hidden="true"></i>
                        </div>

                        <input type="password" id="password_confirmation" name="password_confirmation"
                            value="<?= htmlspecialchars('') ?>" placeholder="New Password_confirmation"
                            aria-label="Password"
                            class="block w-full pl-10 pr-3 py-2 border border-green-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
                            required autocomplete="new-password" autofocus>
                    </div>


                    {{-- <div>
                <label for="password_confirmation" class="flex items-center">
                    <i class="fas fa-lock text-green-500 mr-2"></i> Confirm Password:
                </label>
                <input type="password" id="password_confirmation" name="password_confirmation"
                    class="block w-full px-3 py-2 border border-green-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm"
                    required>
            </div> --}}

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full bg-green-600 text-white py-1 px-4 rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 mt-2">
                        <i class="fa fa-lock mr-2"></i> Reset Password
                    </button>
        </form>

        <div class="mt-4 text-center">
            <p class="text-sm text-gray-600">
                <a href="{{ route('login') }}" class="text-green-600 hover:text-green-500 font-medium underline">
                    <i class="fas fa-arrow-left"></i> Back to Login
                </a>
            </p>
        </div>
    </div>

    <p class="text-center mt-4 text-gray-600 absolute bottom-6">version 1.0.0</p>
</body>

</html>
