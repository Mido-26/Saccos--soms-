<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Unavailable - 503</title>
    <link rel="stylesheet" href="{{ asset('assets/css/all.min.css') }}">
    <script src="{{ asset('assets/js/taiwind.js') }}"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="text-center">
        <div class="text-gray-600 font-extrabold text-8xl animate-pulse">503</div>
        <p class="text-2xl font-semibold text-gray-800 mt-4">Service Unavailable</p>
        <p class="text-gray-600 mt-2">Our system is under maintenance. Please check back later.</p>

        <a href="{{ route('dashboard') }}"
            class="mt-6 inline-block bg-yellow-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:bg-yellow-700 transition duration-300">
            <i class="fas fa-clock"></i> Retry Later
        </a>
    </div>
</body>

</html>
