<?php

class Character
{
    private $pdo;

    public function __construct($pdo)
    {
        if ($pdo === null) {
            throw new InvalidArgumentException("Objeto PDO no puede ser nulo.");
        }
        $this->pdo = $pdo;
    }

    private function getClassId($className)
    {
        if (empty($className)) {
            return null;
        }
        $stmt = $this->pdo->prepare("SELECT class_id FROM classes WHERE class_name = :class_name LIMIT 1");
        $stmt->execute([':class_name' => $className]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['class_id'] : null;
    }

    public function getAll()
    {
        $stmt = $this->pdo->query("
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
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        if (empty($data['name'])) {
            throw new InvalidArgumentException('El nombre es obligatorio.');
        }
        $level = $data['level'] ?? 1;
        if ($level > 33) {
            throw new InvalidArgumentException('El nivel máximo es 33.');
        }
        $attributes = $data['attributes'] ?? [];
        $max_attr = ($level == 1) ? 18 : 20;
        foreach ($attributes as $attr => $value) {
            if (!is_numeric($value) || $value < 8 || $value > $max_attr) {
                throw new InvalidArgumentException("Los atributos deben estar entre 8 y {$max_attr}. Error en {$attr}.");
            }
        }

        $class_id = $this->getClassId($data['class'] ?? null);

        $sql = "INSERT INTO player_character
            (character_name, race, class_id, description, level, strength, dexterity, constitution, intelligence, wisdom, charisma, max_hp, current_hp)
            VALUES
            (:character_name, :race, :class_id, :description, :level, :strength, :dexterity, :constitution, :intelligence, :wisdom, :charisma, :max_hp, :current_hp)";
        $stmt = $this->pdo->prepare($sql);

        $constitution = $data['attributes']['Constitución'] ?? 10;
        $hp_bonus = floor(($constitution - 10) / 2);
        $max_hp = 10 + $hp_bonus;

        $params = [
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

        return $stmt->execute($params);
    }

    public function update($id, $data)
    {
        if (empty($data['name'])) {
            throw new InvalidArgumentException('Faltan datos para actualizar el personaje.');
        }
        $level = $data['level'] ?? 1;
        if ($level > 33) {
            throw new InvalidArgumentException('El nivel máximo es 33.');
        }
        $attributes = $data['attributes'] ?? [];
        $max_attr = ($level == 1) ? 18 : 20;
        foreach ($attributes as $attr => $value) {
            if (!is_numeric($value) || $value < 8 || $value > $max_attr) {
                throw new InvalidArgumentException("Los atributos deben estar entre 8 y {$max_attr}. Error en {$attr}.");
            }
        }

        $class_id = $this->getClassId($data['class'] ?? null);

        $sql = "UPDATE player_character SET
                    character_name = :character_name, race = :race, class_id = :class_id,
                    description = :description, level = :level, strength = :strength,
                    dexterity = :dexterity, constitution = :constitution, intelligence = :intelligence,
                    wisdom = :wisdom, charisma = :charisma, max_hp = :max_hp, current_hp = :current_hp
                WHERE character_id = :character_id";

        $stmt = $this->pdo->prepare($sql);

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
        return $stmt->rowCount();
    }

    public function delete($id)
    {
        if (!$id) {
            throw new InvalidArgumentException('No se proporcionó ID del personaje.');
        }
        $sql = "DELETE FROM player_character WHERE character_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount();
    }
}