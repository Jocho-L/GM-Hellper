<?php
// Habilitar la visualización de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Siempre establecer la cabecera JSON primero
header('Content-Type: application/json');

try {
    // Incluir el archivo de conexión a la base de datos
    require_once __DIR__ . '/../../config/Server.php';

    // Verificar si la conexión a la BD ($pdo) se estableció correctamente
    if (!isset($pdo)) {
        throw new Exception('Error crítico: La variable $pdo no está definida. Verifique el archivo Server.php.');
    }

    // Leer el cuerpo de la petición (que viene en formato JSON)
    $input = json_decode(file_get_contents('php://input'), true);

    // Determinar la acción a realizar
    $action = $_GET['action'] ?? $input['action'] ?? null;

    switch ($action) {
        case 'get_characters':
            $stmt = $pdo->query("
                SELECT
                    pc.character_id, pc.character_name, pc.race, pc.level,
                    pc.strength, pc.dexterity, pc.constitution, pc.intelligence, pc.wisdom, pc.charisma,
                    pc.max_hp, pc.current_hp, pc.description, pc.created_at,
                    c.class_name, c.hit_dice
                FROM
                    player_character pc
                LEFT JOIN
                    classes c ON pc.class_id = c.class_id
                ORDER BY
                    pc.created_at DESC
            ");
            $characters = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $characters]);
            break;

        case 'save_character':
            $data = $input['data'] ?? null;

            if (!$data || empty($data['name'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'El nombre es obligatorio.']);
                exit;
            }

            $level = $data['level'] ?? 1;
            if ($level > 33) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'El nivel máximo es 33.']);
                exit;
            }

            $attributes = $data['attributes'] ?? [];
            $max_attr = ($level == 1) ? 18 : 20;
            foreach ($attributes as $attr => $value) {
                if (!is_numeric($value) || $value < 8 || $value > $max_attr) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => "Los atributos deben estar entre 8 y {$max_attr}. Error en {$attr}."]);
                    exit;
                }
            }

            // Buscar el ID de la clase a partir del nombre
            $stmt_class = $pdo->prepare("SELECT class_id FROM classes WHERE class_name = :class_name LIMIT 1");
            $stmt_class->execute([':class_name' => $data['class'] ?? '']);
            $class_result = $stmt_class->fetch(PDO::FETCH_ASSOC);
            $class_id = $class_result ? $class_result['class_id'] : null;

            $sql = "INSERT INTO player_character
                (character_name, race, class_id, description, level, strength, dexterity, constitution, intelligence, wisdom, charisma, max_hp, current_hp)
                VALUES
                (:character_name, :race, :class_id, :description, :level, :strength, :dexterity, :constitution, :intelligence, :wisdom, :charisma, :max_hp, :current_hp)";
            $stmt = $pdo->prepare($sql);

            $constitution = $data['attributes']['Constitución'] ?? 10;
            $hp_bonus = floor(($constitution - 10) / 2);
            $max_hp = 10 + $hp_bonus;

            $stmt->execute([
                ':character_name' => $data['name'],
                ':race'           => $data['race'],
                ':class_id'       => $class_id,
                ':description'    => $data['description'] ?? '',
                ':level'          => $level,
                ':strength'       => $data['attributes']['Fuerza'] ?? 10,
                ':dexterity'      => $data['attributes']['Destreza'] ?? 10,
                ':constitution'   => $constitution,
                ':intelligence'   => $data['attributes']['Inteligencia'] ?? 10,
                ':wisdom'         => $data['attributes']['Sabiduría'] ?? 10,
                ':charisma'       => $data['attributes']['Carisma'] ?? 10,
                ':max_hp'         => $max_hp,
                ':current_hp'     => $max_hp
            ]);

            echo json_encode(['success' => true, 'message' => 'Personaje guardado con éxito.']);
            break;

        case 'update_character':
            $id = $input['character_id'] ?? null;
            $data = $input['data'] ?? null;

            if (!$id || !$data || empty($data['name'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Faltan datos para actualizar el personaje.']);
                exit;
            }

            $level = $data['level'] ?? 1;
            if ($level > 33) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'El nivel máximo es 33.']);
                exit;
            }

            $attributes = $data['attributes'] ?? [];
            $max_attr = ($level == 1) ? 18 : 20;
            foreach ($attributes as $attr => $value) {
                if (!is_numeric($value) || $value < 8 || $value > $max_attr) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => "Los atributos deben estar entre 8 y {$max_attr}. Error en {$attr}."]);
                    exit;
                }
            }

            // Buscar el ID de la clase a partir del nombre
            $stmt_class = $pdo->prepare("SELECT class_id FROM classes WHERE class_name = :class_name LIMIT 1");
            $stmt_class->execute([':class_name' => $data['class'] ?? '']);
            $class_result = $stmt_class->fetch(PDO::FETCH_ASSOC);
            $class_id = $class_result ? $class_result['class_id'] : null;

            $sql = "UPDATE player_character SET
                        character_name = :character_name,
                        race = :race,
                        class_id = :class_id,
                        description = :description,
                        level = :level,
                        strength = :strength,
                        dexterity = :dexterity,
                        constitution = :constitution,
                        intelligence = :intelligence,
                        wisdom = :wisdom,
                        charisma = :charisma,
                        max_hp = :max_hp,
                        current_hp = :current_hp
                    WHERE character_id = :character_id";

            $stmt = $pdo->prepare($sql);

            $constitution = $data['attributes']['Constitución'] ?? 10;
            $hp_bonus = floor(($constitution - 10) / 2);
            $max_hp = 10 + $hp_bonus;

            $params = [
                ':character_id'   => $id,
                ':character_name' => $data['name'],
                ':race'           => $data['race'],
                ':class_id'       => $class_id,
                ':description'    => $data['description'] ?? '',
                ':level'          => $level,
                ':strength'       => $data['attributes']['Fuerza'] ?? 10,
                ':dexterity'      => $data['attributes']['Destreza'] ?? 10,
                ':constitution'   => $constitution,
                ':intelligence'   => $data['attributes']['Inteligencia'] ?? 10,
                ':wisdom'         => $data['attributes']['Sabiduría'] ?? 10,
                ':charisma'       => $data['attributes']['Carisma'] ?? 10,
                ':max_hp'         => $max_hp,
                ':current_hp'     => $max_hp
            ];

            $stmt->execute($params);

            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Personaje actualizado con éxito.']);
            } else {
                echo json_encode(['success' => true, 'message' => 'No se realizaron cambios en el personaje.']);
            }
            break;

        case 'delete_character':
            $id = $input['character_id'] ?? null;

            if (!$id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'No se proporcionó ID del personaje.']);
                exit;
            }
            $sql = "DELETE FROM player_character WHERE character_id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);

            if ($stmt->rowCount() > 0) {
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

} catch (PDOException $e) {
    // Captura errores específicos de la base de datos
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()]);
} catch (Throwable $e) { // Throwable captura todo tipo de errores, incluyendo los fatales de 'require'
    // Captura cualquier otro error (como el require_once fallido)
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error Crítico del Servidor: ' . $e->getMessage(), 'file' => $e->getFile(), 'line' => $e->getLine()]);
}
?>
