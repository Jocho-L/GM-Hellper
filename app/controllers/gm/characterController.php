<?php
// Habilitar la visualización de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Siempre establecer la cabecera JSON primero
header('Content-Type: application/json');

try {
    // Incluir dependencias
    require_once __DIR__ . '/../../config/Server.php';
    require_once __DIR__ . '/../../models/Character.php';

    // Verificar si la conexión a la BD ($pdo) se estableció correctamente
    if (!isset($pdo)) {
        throw new Exception('Error crítico: La variable $pdo no está definida. Verifique el archivo Server.php.');
    }

    // Instanciar el modelo
    $characterModel = new Character($pdo);

    // Leer el cuerpo de la petición (que viene en formato JSON)
    $input = json_decode(file_get_contents('php://input'), true);

    // Determinar la acción a realizar
    $action = $_GET['action'] ?? $input['action'] ?? null;

    switch ($action) {
        case 'get_characters':
            $characters = $characterModel->getAll();
            echo json_encode(['success' => true, 'data' => $characters]);
            break;

        case 'save_character':
            $data = $input['data'] ?? null;
            if (!$data) {
                 throw new InvalidArgumentException('No se proporcionaron datos para guardar.');
            }
            $characterModel->create($data);
            echo json_encode(['success' => true, 'message' => 'Personaje guardado con éxito.']);
            break;

        case 'update_character':
            $id = $input['character_id'] ?? null;
            $data = $input['data'] ?? null;

            if (!$id || !$data) {
                throw new InvalidArgumentException('Faltan datos para actualizar el personaje.');
            }

            $rowCount = $characterModel->update($id, $data);

            if ($rowCount > 0) {
                echo json_encode(['success' => true, 'message' => 'Personaje actualizado con éxito.']);
            } else {
                echo json_encode(['success' => true, 'message' => 'No se realizaron cambios en el personaje.']);
            }
            break;

        case 'delete_character':
            $id = $input['character_id'] ?? null;
            if (!$id) {
                throw new InvalidArgumentException('No se proporcionó ID del personaje.');
            }

            $rowCount = $characterModel->delete($id);

            if ($rowCount > 0) {
                echo json_encode(['success' => true, 'message' => 'Personaje eliminado.']);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'No se encontró el personaje.']);
            }
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Acción no válida.']);
            break;
    }

} catch (InvalidArgumentException $e) {
    // Captura errores de validación del modelo
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} catch (PDOException $e) {
    // Captura errores específicos de la base de datos
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()]);
} catch (Throwable $e) { // Throwable captura todo tipo de errores
    // Captura cualquier otro error
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error Crítico del Servidor: ' . $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
}
?>
