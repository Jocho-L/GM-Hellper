<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Creador de Personajes de Rol (PHP + AJAX)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=MedievalSharp&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../../public/css/GM/crear_pc.css">
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-7xl mx-auto">
        <h1 class="font-medieval text-5xl text-center mb-2 text-purple-400">Creador de Personajes</h1>
        <p class="text-center text-gray-400 mb-8">Versión con Backend Simulado</p>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Columna de Creación -->
            <div class="card p-6 rounded-lg shadow-lg">
                <h2 class="font-medieval text-3xl mb-6 text-purple-300">Crear Nuevo Héroe</h2>
                <input type="hidden" id="char-id">

                <div class="space-y-4">
                    <div>
                        <label for="char-name" class="block text-sm font-bold mb-1 text-gray-300">Nombre del Personaje</label>
                        <input type="text" id="char-name" placeholder="Ej: Arion, el Valiente" class="w-full p-2 rounded-md">
                    </div>

                    <div class="grid grid-cols-4 gap-4">
                        <div>
                            <label for="char-race" class="block text-sm font-bold mb-1 text-gray-300">Raza</label>
                            <select id="char-race" class="w-full p-2 rounded-md">
                                <option>Humano</option>
                                <option>Elfo</option>
                                <option>Enano</option>
                                <option>Orco</option>
                                <option>Mediano</option>
                            </select>
                        </div>
                        <div>
                            <label for="char-class" class="block text-sm font-bold mb-1 text-gray-300">Clase</label>
                            <select id="char-class" class="w-full p-2 rounded-md">
                                <option>Guerrero</option>
                                <option>Mago</option>
                                <option>Pícaro</option>
                                <option>Clérigo</option>
                                <option>Explorador</option>
                            </select>
                        </div>
                        <div>
                            <label for="char-level" class="block text-sm font-bold mb-1 text-gray-300">Nivel</label>
                            <input type="number" id="char-level" value="1" min="1" max="33" class="w-full p-2 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-bold mb-1 text-gray-300">Dado de Golpe</label>
                            <p id="char-hit-dice" class="w-full p-2 rounded-md bg-gray-800 text-white text-center font-semibold"></p>
                        </div>
                    </div>

                    <div>
                        <h3 class="font-semibold text-lg mb-2 text-gray-300">Atributos</h3>
                        <p class="text-sm text-gray-400 mb-2">Total de Puntos a distribuir: <span id="total-points">72</span></p>
                        <p class="text-sm text-gray-400 mb-4">Puntos Gastados: <span id="spent-points">48</span> / <span id="total-points-display">72</span></p>
                        <div id="attributes" class="space-y-3"></div>
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

            <!-- Columna de Ficha de Personaje -->
            <div id="character-sheet-card" class="card p-6 rounded-lg shadow-lg">
                <h2 class="font-medieval text-3xl mb-6 text-purple-300">Ficha de Personaje</h2>
                <div id="character-sheet-content" class="space-y-4">
                    <p class="text-gray-400 text-center mt-8">Carga un personaje para ver sus detalles aquí.</p>
                </div>
            </div>

            <!-- Columna de Personajes Guardados -->
            <div class="card p-6 rounded-lg shadow-lg">
                <h2 class="font-medieval text-3xl mb-6 text-purple-300">Héroes Guardados</h2>
                <div id="saved-characters-list" class="space-y-3 max-h-96 overflow-y-auto pr-2">
                    <p class="text-gray-400">Cargando personajes...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para mostrar mensajes -->
    <div id="message-modal" class="modal-backdrop hidden">
        <div class="modal-content text-center">
            <p id="modal-message" class="text-lg mb-4"></p>
            <button id="modal-close-btn" class="btn-primary text-white font-bold py-2 px-4 rounded-md">Cerrar</button>
        </div>
    </div>

    <script>
        // --- CONFIGURACIÓN ---
        const API_URL = '/GM-Hellper/app/controllers/gm/crearPJController.php';

        // Elementos del DOM
        const attributesContainer = document.getElementById('attributes');
        const generateBtn = document.getElementById('generate-btn');
        const saveBtn = document.getElementById('save-btn');
        const clearBtn = document.getElementById('clear-btn');
        const savedCharactersList = document.getElementById('saved-characters-list');
        const charIdInput = document.getElementById('char-id');
        const charClassSelect = document.getElementById('char-class');
        const charHitDiceEl = document.getElementById('char-hit-dice');
        const charLevelInput = document.getElementById('char-level');
        const totalPointsSpan = document.getElementById('total-points');
        const spentPointsSpan = document.getElementById('spent-points');
        const totalPointsDisplaySpan = document.getElementById('total-points-display');

        // Modal de mensajes
        const messageModal = document.getElementById('message-modal');
        const modalMessage = document.getElementById('modal-message');
        const modalCloseBtn = document.getElementById('modal-close-btn');

        // Atributos del personaje
        const ATTRIBUTES = ['Fuerza', 'Destreza', 'Constitución', 'Inteligencia', 'Sabiduría', 'Carisma'];
        const ATTRIBUTE_MAP_DB = {
            'Fuerza': 'strength',
            'Destreza': 'dexterity',
            'Constitución': 'constitution',
            'Inteligencia': 'intelligence',
            'Sabiduría': 'wisdom',
            'Carisma': 'charisma'
        };
        const CLASS_HIT_DICE = {
            'Guerrero': '1d10',
            'Mago': '1d6',
            'Pícaro': '1d8',
            'Clérigo': '1d8',
            'Explorador': '1d10'
        };
        const MAX_ATTRIBUTE_SCORE = 20;
        const MIN_ATTRIBUTE_SCORE = 8;
        const BASE_TOTAL_POINTS = 72;

        // --- FUNCIONES ---

        function getMaxAttributeScore() {
            const level = parseInt(charLevelInput.value, 10) || 1;
            return (level === 1) ? 18 : MAX_ATTRIBUTE_SCORE;
        }

        function calculateTotalPoints(level) {
            if (level <= 1) {
                return BASE_TOTAL_POINTS;
            }
            let points = BASE_TOTAL_POINTS;
            for (let i = 2; i <= level; i++) {
                points += (i % 2 === 0) ? 2 : 1; // +2 si es par, +1 si es impar
            }
            return points;
        }

        function updateSpentPoints() {
            let currentTotalSpent = 0;
            ATTRIBUTES.forEach(attr => {
                const valEl = document.getElementById(`val-${attr.toLowerCase()}`);
                if (valEl) {
                    currentTotalSpent += parseInt(valEl.textContent, 10);
                }
            });
            if (spentPointsSpan) {
                spentPointsSpan.textContent = currentTotalSpent;
            }
            const totalPointsBudget = parseInt(totalPointsSpan.textContent, 10);
            if (spentPointsSpan) {
                if (currentTotalSpent > totalPointsBudget) {
                    spentPointsSpan.classList.add('text-red-500', 'font-bold');
                } else {
                    spentPointsSpan.classList.remove('text-red-500', 'font-bold');
                }
            }
        }

        function updateTotalPoints() {
            const level = parseInt(charLevelInput.value, 10) || 1;
            const totalPoints = calculateTotalPoints(level);
            totalPointsSpan.textContent = totalPoints;
            if (totalPointsDisplaySpan) {
                totalPointsDisplaySpan.textContent = totalPoints;
            }

            // Re-validar y ajustar atributos si el nivel cambia y excede el máximo permitido
            const maxScore = getMaxAttributeScore();
            ATTRIBUTES.forEach(attr => {
                const lowerAttr = attr.toLowerCase();
                const rangeInput = document.getElementById(`range-${lowerAttr}`);
                if (rangeInput) {
                    rangeInput.max = maxScore;
                }

                const valEl = document.getElementById(`val-${lowerAttr}`);
                if (valEl) {
                    const currentValue = parseInt(valEl.textContent, 10);
                    if (currentValue > maxScore) {
                        updateAttributeUI(attr, maxScore);
                    }
                }
            });

            updateSpentPoints();
        }

        function showMessage(message) {
            modalMessage.textContent = message;
            messageModal.classList.remove('hidden');
        }

        modalCloseBtn.addEventListener('click', () => {
            messageModal.classList.add('hidden');
        });

        function updateHitDice() {
            const selectedClass = charClassSelect.value;
            charHitDiceEl.textContent = CLASS_HIT_DICE[selectedClass] || 'N/A';
        }

        function renderAttributeInputs() {
            attributesContainer.innerHTML = '';
            ATTRIBUTES.forEach(attr => {
                const lowerAttr = attr.toLowerCase();
                const div = document.createElement('div');
                div.className = 'space-y-2';
                div.innerHTML = `
                    <div class="flex items-center justify-between">
                        <label class="font-bold text-sm text-gray-300">${attr}</label>
                        <div class="flex items-center gap-3">
                            <button class="attr-btn bg-purple-600 hover:bg-purple-700 text-white font-bold w-6 h-6 rounded-full flex items-center justify-center" data-attr="${lowerAttr}" data-change="-1">-</button>
                            <span id="val-${lowerAttr}" class="font-semibold text-lg w-8 text-center text-white">8</span>
                            <button class="attr-btn bg-purple-600 hover:bg-purple-700 text-white font-bold w-6 h-6 rounded-full flex items-center justify-center" data-attr="${lowerAttr}" data-change="1">+</button>
                        </div>
                    </div>
                    <input type="range" min="${MIN_ATTRIBUTE_SCORE}" max="${MAX_ATTRIBUTE_SCORE}" value="8" class="w-full h-2 bg-gray-700 rounded-lg appearance-none cursor-pointer range-slider" id="range-${lowerAttr}">
                `;
                attributesContainer.appendChild(div);
            });

            // Delegación de eventos para los botones +/-
            attributesContainer.addEventListener('click', (e) => {
                if (e.target.classList.contains('attr-btn')) {
                    const lowerAttr = e.target.dataset.attr;
                    const change = parseInt(e.target.dataset.change, 10);
                    const valEl = document.getElementById(`val-${lowerAttr}`);
                    const currentValue = parseInt(valEl.textContent, 10);
                    const attrName = ATTRIBUTES.find(a => a.toLowerCase() === lowerAttr);
                    handleAttributeChange(attrName, currentValue + change);
                }
            });

            // Event listeners para los sliders
            document.querySelectorAll('.range-slider').forEach(slider => {
                slider.addEventListener('input', (e) => {
                    const lowerAttr = e.target.id.replace('range-', '');
                    const attrName = ATTRIBUTES.find(a => a.toLowerCase() === lowerAttr);
                    handleAttributeChange(attrName, parseInt(e.target.value, 10));
                });
            });
        }

        function updateAttributeUI(attr, value) {
            const lowerAttr = attr.toLowerCase();
            const valEl = document.getElementById(`val-${lowerAttr}`);
            const rangeInput = document.getElementById(`range-${lowerAttr}`);
            if (valEl) {
                valEl.textContent = value;
            }
            if (rangeInput) {
                rangeInput.value = value;
            }
        }

        function handleAttributeChange(attr, newValue) {
            const lowerAttr = attr.toLowerCase();
            const valEl = document.getElementById(`val-${lowerAttr}`);
            if (!valEl) return;

            const oldValue = parseInt(valEl.textContent, 10);
            const maxScoreForLevel = getMaxAttributeScore();

            // Limitar el nuevo valor a los máximos/mínimos del atributo
            newValue = Math.max(MIN_ATTRIBUTE_SCORE, Math.min(maxScoreForLevel, newValue));

            if (newValue === oldValue) return;

            // Si se está incrementando el valor, verificar el presupuesto
            if (newValue > oldValue) {
                const totalPointsBudget = parseInt(totalPointsSpan.textContent, 10);
                let currentTotalSpent = 0;
                ATTRIBUTES.forEach(a => {
                    currentTotalSpent += parseInt(document.getElementById(`val-${a.toLowerCase()}`).textContent, 10);
                });

                const pointsAvailable = totalPointsBudget - currentTotalSpent;
                const cost = newValue - oldValue;

                if (cost > pointsAvailable) {
                    // No hay suficientes puntos, ajustar al máximo posible
                    newValue = oldValue + pointsAvailable;
                }
            }

            updateAttributeUI(attr, newValue);
            updateSpentPoints();
        }

        // NOTA: La función makeBarInteractive() ha sido reemplazada por la lógica anterior y debe ser eliminada.

        function generateAttributes() {
            const level = parseInt(charLevelInput.value, 10) || 1;
            const totalPoints = parseInt(totalPointsSpan.textContent, 10);
            let points = totalPoints;
            const scores = {};

            // Determinar los límites de los atributos según el nivel
            const minScore = MIN_ATTRIBUTE_SCORE;
            const maxScore = (level === 1) ? 18 : MAX_ATTRIBUTE_SCORE;

            // Validar si los puntos totales son suficientes
            if (totalPoints < minScore * ATTRIBUTES.length) {
                showMessage(`Puntos insuficientes (${totalPoints}) para los atributos mínimos (${minScore}) en nivel ${level}.`);
                ATTRIBUTES.forEach(attr => updateAttributeUI(attr, minScore));
                return;
            }

            // Inicializar cada atributo con el puntaje mínimo
            ATTRIBUTES.forEach(attr => {
                scores[attr] = minScore;
                points -= minScore;
            });

            // Distribuir los puntos restantes aleatoriamente
            // Se añade un contador de seguridad para evitar bucles infinitos si algo sale mal
            let safetyBreak = 1000;
            while (points > 0 && safetyBreak > 0) {
                const randomAttr = ATTRIBUTES[Math.floor(Math.random() * ATTRIBUTES.length)];
                if (scores[randomAttr] < maxScore) {
                    scores[randomAttr]++;
                    points--;
                }
                safetyBreak--;
            }
            ATTRIBUTES.forEach(attr => updateAttributeUI(attr, scores[attr]));
            updateSpentPoints();
        }

        function getCharacterDataFromUI() {
            const name = document.getElementById('char-name').value;
            const race = document.getElementById('char-race').value;
            const charClass = document.getElementById('char-class').value;
            const level = parseInt(document.getElementById('char-level').value, 10) || 1;
            const description = document.getElementById('char-description').value;
            const attributes = {};
            ATTRIBUTES.forEach(attr => {
                attributes[attr] = parseInt(document.getElementById(`val-${attr.toLowerCase()}`).textContent, 10);
            });
            return { name, race, class: charClass, level, description, attributes };
        }

        function resetCreatorForm() {
            charIdInput.value = '';
            document.getElementById('char-name').value = '';
            document.getElementById('char-race').value = 'Humano';
            document.getElementById('char-class').value = 'Guerrero';
            document.getElementById('char-level').value = 1;
            document.getElementById('char-description').value = '';
            saveBtn.textContent = 'Guardar Personaje';
            updateTotalPoints();
            generateAttributes();
            updateHitDice();
        }

        function loadCharacterIntoCreator(characterData) {
            charIdInput.value = characterData.character_id;
            document.getElementById('char-name').value = characterData.character_name;
            document.getElementById('char-race').value = characterData.race;
            document.getElementById('char-class').value = characterData.class_name;
            document.getElementById('char-level').value = characterData.level || 1;
            document.getElementById('char-description').value = characterData.description || '';

            updateTotalPoints(); // Mover aquí para establecer los máximos correctos

            ATTRIBUTES.forEach(attr => {
                const dbKey = ATTRIBUTE_MAP_DB[attr];
                updateAttributeUI(attr, characterData[dbKey] || 8);
            });
            saveBtn.textContent = 'Actualizar Personaje';
            updateHitDice();
            updateSpentPoints(); // Asegurarse de que los puntos gastados se actualicen
        }

        async function loadCharacters() {
            try {
                const response = await fetch(`${API_URL}?action=get_characters`);
                if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
                const result = await response.json();

                if (result.success) {
                    renderSavedCharacters(result.data);
                } else {
                    showMessage(result.message || 'Error al cargar personajes.');
                }
            } catch (error) {
                console.error('Error en loadCharacters:', error);
                showMessage('No se pudo conectar con el servidor.');
                savedCharactersList.innerHTML = '<p class="text-red-400">Error al cargar los personajes.</p>';
            }
        }

        function renderSavedCharacters(characters) {
            savedCharactersList.innerHTML = '';
            if (!characters || characters.length === 0) {
                savedCharactersList.innerHTML = '<p class="text-gray-400">No hay personajes guardados.</p>';
                return;
            }

            characters.forEach(char => {
                const div = document.createElement('div');
                div.className = 'card p-3 rounded-md flex justify-between items-center bg-gray-700/50';
                div.innerHTML = `
                    <div>
                        <p class="font-bold text-purple-300">${char.character_name}</p>
                        <p class="text-sm text-gray-400">${char.race} ${char.class_name || 'Sin Clase'} - Nivel ${char.level} (${char.hit_dice || 'N/A'})</p>
                    </div>
                    <div class="flex gap-2">
                        <button data-id="${char.character_id}" class="load-char-btn btn text-xs py-1 px-2 rounded">Cargar</button>
                        <button data-id="${char.character_id}" class="delete-char-btn btn-primary text-xs py-1 px-2 rounded bg-red-600 hover:bg-red-700">X</button>
                    </div>
                `;
                savedCharactersList.appendChild(div);
            });

            document.querySelectorAll('.load-char-btn').forEach(button => {
                button.addEventListener('click', (e) => {
                    const charId = e.target.dataset.id;
                    const selectedChar = characters.find(c => c.character_id == charId);
                    if (selectedChar) {
                        loadCharacterIntoCreator(selectedChar);
                        showMessage(`Personaje '${selectedChar.character_name}' cargado para edición.`);
                    }
                });
            });

            document.querySelectorAll('.delete-char-btn').forEach(button => {
                button.addEventListener('click', (e) => {
                    const charId = e.target.dataset.id;
                    if (confirm('¿Estás seguro de que quieres eliminar este personaje?')) {
                        deleteCharacter(charId);
                    }
                });
            });
        }

        async function saveOrUpdateCharacter() {
            const characterData = getCharacterDataFromUI();
            const charId = charIdInput.value;

            if (!characterData.name) {
                showMessage("El personaje debe tener un nombre para ser guardado.");
                return;
            }

            const action = charId ? 'update_character' : 'save_character';
            const payload = {
                action: action,
                data: characterData
            };
            if (charId) {
                payload.character_id = charId;
            }

            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                if (!response.ok) {
                    const errorText = await response.text();
                    throw new Error(`Error HTTP ${response.status}: ${errorText}`);
                }

                const result = await response.json();
                showMessage(result.message);

                if (result.success) {
                    loadCharacters(); // Recargar la lista
                    resetCreatorForm(); // Limpiar el formulario tras guardar/actualizar
                }
            } catch (error) {
                console.error('Error en saveOrUpdateCharacter:', error);
                showMessage('No se pudo conectar con el servidor para guardar/actualizar.');
            }
        }

        async function deleteCharacter(charId) {
            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'delete_character',
                        character_id: charId
                    })
                });

                const result = await response.json();
                showMessage(result.message);

                if (result.success) {
                    loadCharacters(); // Recargar la lista
                }
            } catch (error) {
                console.error('Error en deleteCharacter:', error);
                showMessage('No se pudo conectar con el servidor para eliminar.');
            }
        }

        // --- INICIALIZACIÓN ---
        function initialize() {
            renderAttributeInputs();
            updateTotalPoints();
            generateAttributes();
            loadCharacters();
            updateHitDice();

            // Añadir listeners para los botones principales
            generateBtn.addEventListener('click', generateAttributes);
            saveBtn.addEventListener('click', saveOrUpdateCharacter);
            clearBtn.addEventListener('click', resetCreatorForm);
            charClassSelect.addEventListener('change', updateHitDice);
            charLevelInput.addEventListener('change', updateTotalPoints);
        }

        initialize();
    </script>
</body>
</html>
