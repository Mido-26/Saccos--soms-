<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internal Server Error - 500</title>
    <link rel="stylesheet" href="{{ asset('assets/css/all.min.css') }}">
    <script src="{{ asset('assets/js/taiwind.js') }}"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="text-center">
        <div class="text-gray-600 font-extrabold text-8xl animate-pulse">500</div>
        <p class="text-2xl font-semibold text-gray-800 mt-4">Internal Server Error</p>
        <p class="text-gray-600 mt-2">Oops! Something went wrong on our side. Please try again later.</p>

        <a href="{{ route('dashboard') }}"
            class="mt-6 inline-block bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg shadow-md hover:bg-gray-700 transition duration-300">
            <i class="fas fa-redo-alt"></i> Go Back Home
        </a>
    </div>
</body>

</html>
