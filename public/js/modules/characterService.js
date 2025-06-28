// This file contains functions for making API calls to the backend, such as fetchCharacters and saveCharacter.
// It handles the communication between the frontend and the server.

const API_URL = '/GM-Hellper/app/controllers/characterController.php';

async function fetchCharacters() {
    try {
        const response = await fetch(`${API_URL}?action=get_characters`);
        if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
        const result = await response.json();
        return result.data;
    } catch (error) {
        console.error('Error fetching characters:', error);
        throw error;
    }
}

async function saveCharacter(characterData) {
    const action = characterData.id ? 'update_character' : 'save_character';
    const payload = {
        action: action,
        data: characterData
    };

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
        return result;
    } catch (error) {
        console.error('Error saving character:', error);
        throw error;
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
        return result;
    } catch (error) {
        console.error('Error deleting character:', error);
        throw error;
    }
}