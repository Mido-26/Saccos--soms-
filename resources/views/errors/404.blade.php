<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oops! Page Not Found</title>
    <link rel="stylesheet" href="{{ asset('assets/css/all.min.css') }}">
    <script src="{{ asset('assets/js/taiwind.js') }}"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="text-center">
        <div class="text-gray-600 font-extrabold text-8xl animate-pulse">404</div>
        <p class="text-2xl font-semibold text-gray-800 mt-4">Oops! Page Not Found</p>
        <p class="text-gray-600 mt-2">The page you are looking for might have been removed or is temporarily
            unavailable.</p>

        <a href="{{ route('dashboard') }}"
            class="mt-6 inline-block bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:bg-blue-700 transition duration-300">
            <i class="fas fa-home"></i> Return to Homepage
        </a>
    </div>
</body>


</html>
