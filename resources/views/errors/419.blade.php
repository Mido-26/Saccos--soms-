<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Expired - 419</title>
    <link rel="stylesheet" href="{{ asset('assets/css/all.min.css') }}">
    <script src="{{ asset('assets/js/taiwind.js') }}"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="text-center">
        <div class="text-gray-600 font-extrabold text-8xl animate-pulse">419</div>
        <p class="text-2xl font-semibold text-gray-800 mt-4">Session Expired</p>
        <p class="text-gray-600 mt-2">Your session has expired due to inactivity. Please refresh the page or log in again.</p>

        <a href="{{ route('login') }}"
            class="mt-6 inline-block bg-red-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:bg-red-700 transition duration-300">
            <i class="fas fa-sign-in-alt"></i> Re-login
        </a>
    </div>
</body>

</html>
