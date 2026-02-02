<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Impulso Norte</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="antialiased flex flex-col min-h-screen bg-gray-100 text-gray-800">
    <div class="flex-grow flex items-center justify-center">
        <div class="text-center p-6 bg-white rounded-xl shadow-lg">
            <h1 class="text-4xl font-bold mb-4 text-indigo-600">Bienvenido a Impulso Norte</h1>
            <p class="text-lg mb-6">Sistema de Gestión Administrativa</p>
            <div class="space-x-4">
                <a href="/admin"
                    class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-500 transition shadow-md">Ingresar
                    al Panel</a>
            </div>
        </div>
    </div>

    <footer
        class="py-6 text-center text-sm text-gray-500 border-t border-gray-200 bg-white">
        <p>&copy; {{ date('Y') }} Impulso Norte. Todos los derechos reservados.</p>
    </footer>
</body>

</html>