<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Creador de Personajes</title>
    <script src="/GM-Hellper/public/js/characterCreator.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=MedievalSharp&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../../public/css/GM/crear_pc.css">
</head>

<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-7xl mx-auto">
        <h1 class="font-medieval text-5xl text-center mb-2 text-purple-400">Creador de Personajes</h1>
        <p class="text-center text-gray-400 mb-8">Versión con Backend Simulado</p>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="card p-6 rounded-lg shadow-lg">
                <h2 class="font-medieval text-3xl mb-6 text-purple-300">Crear Nuevo Héroe</h2>
                <input type="hidden" id="char-id">

                <div class="space-y-4">
                    <div>
                        <label for="char-name" class="block text-sm font-bold mb-1 text-gray-300">Nombre del Héroe</label>
                        <input type="text" id="char-name" placeholder="Nombre del héroe" class="w-full p-2 rounded-md bg-gray-800 text-white border border-gray-600 focus:border-purple-500 focus:ring focus:ring-purple-500 focus:ring-opacity-50">
                    </div>

                    <div>
                        <label for="char-race" class="block text-sm font-bold mb-1 text-gray-300">Raza</label>
                        <select id="char-race" class="w-full p-2 rounded-md bg-gray-800 text-white border border-gray-600 focus:border-purple-500 focus:ring focus:ring-purple-500 focus:ring-opacity-50">
                            <option value="Humano">Humano</option>
                            <option value="Elfo">Elfo</option>
                            <option value="Enano">Enano</option>
                            <option value="Orco">Orco</option>
                        </select>
                    </div>

                    <div>
                        <label for="char-class" class="block text-sm font-bold mb-1 text-gray-300">Clase</label>
                        <select id="char-class" class="w-full p-2 rounded-md bg-gray-800 text-white border border-gray-600 focus:border-purple-500 focus:ring focus:ring-purple-500 focus:ring-opacity-50">
                            <option value="Guerrero">Guerrero</option>
                            <option value="Mago">Mago</option>
                            <option value="Pícaro">Pícaro</option>
                            <option value="Clérigo">Clérigo</option>
                        </select>
                    </div>

                    <div>
                        <label for="char-level" class="block text-sm font-bold mb-1 text-gray-300">Nivel</label>
                        <input type="number" id="char-level" min="1" value="1" class="w-full p-2 rounded-md bg-gray-800 text-white border border-gray-600 focus:border-purple-500 focus:ring focus:ring-purple-500 focus:ring-opacity-50">
                    </div>

                    <div>
                        <label for="char-description" class="block text-sm font-bold mb-1 text-gray-300">Descripción e Historia</label>
                        <textarea id="char-description" rows="3" placeholder="Describe la apariencia, personalidad e historia de tu héroe..." class="w-full p-2 rounded-md bg-gray-800 text-white border border-gray-600 focus:border-purple-500 focus:ring focus:ring-purple-500 focus:ring-opacity-50"></textarea>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 pt-4">
                        <button id="generate-btn" class="btn-primary text-white font-bold py-2 px-4 rounded-md w-full shadow-md">Generar Atributos</button>
                        <button id="save-btn" class="btn text-gray-200 font-bold py-2 px-4 rounded-md w-full shadow-md">Guardar Personaje</button>
                        <button id="clear-btn" class="btn bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-md w-full shadow-md">Limpiar Formulario</button>
                    </div>
                </div>
            </div>

            <div id="character-sheet-card" class="card p-6 rounded-lg shadow-lg">
                <h2 class="font-medieval text-3xl mb-6 text-purple-300">Ficha de Personaje</h2>
                <div id="character-sheet-content" class="space-y-4">
                    <p class="text-gray-400 text-center mt-8">Carga un personaje para ver sus detalles aquí.</p>
                </div>
            </div>

            <div class="card p-6 rounded-lg shadow-lg">
                <h2 class="font-medieval text-3xl mb-6 text-purple-300">Héroes Guardados</h2>
                <div id="saved-characters-list" class="space-y-3 max-h-96 overflow-y-auto pr-2">
                    <p class="text-gray-400">Cargando personajes...</p>
                </div>
            </div>
        </div>
    </div>

    <div id="message-modal" class="modal-backdrop hidden">
        <div class="modal-content text-center">
            <p id="modal-message" class="text-lg mb-4"></p>
            <button id="modal-close-btn" class="btn-primary text-white font-bold py-2 px-4 rounded-md">Cerrar</button>
        </div>
    </div>
</body>

</html>