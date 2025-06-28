// This is the main JavaScript entry point for the application.
// It initializes the application and sets up event listeners for user interactions.

import { fetchCharacters, saveCharacter } from './modules/characterService.js';
import { renderCharacterList, showMessage } from './modules/domController.js';

document.addEventListener('DOMContentLoaded', () => {
    initialize();
});

function initialize() {
    loadCharacters();
    setupEventListeners();
}

function setupEventListeners() {
    const saveBtn = document.getElementById('save-btn');
    const clearBtn = document.getElementById('clear-btn');

    saveBtn.addEventListener('click', saveCharacterHandler);
    clearBtn.addEventListener('click', resetForm);
}

function saveCharacterHandler() {
    const characterData = getCharacterDataFromUI();
    if (!characterData.name) {
        showMessage("El personaje debe tener un nombre para ser guardado.");
        return;
    }
    saveCharacter(characterData)
        .then(response => {
            showMessage(response.message);
            loadCharacters();
        })
        .catch(error => {
            showMessage('Error al guardar el personaje.');
        });
}

function loadCharacters() {
    fetchCharacters()
        .then(characters => {
            renderCharacterList(characters);
        })
        .catch(error => {
            showMessage('Error al cargar los personajes.');
        });
}

function resetForm() {
    // Logic to reset the character creation form
}

function getCharacterDataFromUI() {
    // Logic to gather character data from the UI
}