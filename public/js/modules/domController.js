// This file contains functions for updating the user interface, such as renderCharacterList and showMessage.
// It manages the display of character data and user notifications.

function renderCharacterList(characters) {
    const characterListContainer = document.getElementById('saved-characters-list');
    characterListContainer.innerHTML = '';

    if (!characters || characters.length === 0) {
        characterListContainer.innerHTML = '<p class="text-gray-400">No hay personajes guardados.</p>';
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
        characterListContainer.appendChild(div);
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

function showMessage(message) {
    const messageModal = document.getElementById('message-modal');
    const modalMessage = document.getElementById('modal-message');
    modalMessage.textContent = message;
    messageModal.classList.remove('hidden');
}