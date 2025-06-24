-- Ya creada previamente
DROP DATABASE IF EXISTS GM_Helper;
CREATE DATABASE IF NOT EXISTS GM_Helper;
USE GM_Helper;

-- Usuarios
CREATE TABLE user (
    user_id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username    VARCHAR(50) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    email       VARCHAR(100) NOT NULL UNIQUE,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Clases de personaje
CREATE TABLE classes (
    class_id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    class_name  VARCHAR(50) NOT NULL UNIQUE,
    hit_dice    VARCHAR(5) NOT NULL -- Ej: 'd6', 'd8', etc.
);

-- Armas
CREATE TABLE weapons (
    weapon_id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    weapon_name VARCHAR(100) NOT NULL,
    dice_sides  INT NOT NULL,
    dice_count  INT NOT NULL,
    `range`     TINYINT UNSIGNED NOT NULL, -- Rango de la arma
    description TEXT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Mesas
CREATE TABLE desk (
    desk_id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    desk_name   VARCHAR(100) NOT NULL,
    description_desk TEXT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_GM       INT UNSIGNED,
    FOREIGN KEY (id_GM) REFERENCES user (user_id) ON DELETE SET NULL
);

-- Personajes jugadores
CREATE TABLE player_character (
    character_id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    player_id       INT UNSIGNED,
    desk_id         INT UNSIGNED,
    class_id        INT UNSIGNED,
    weapon_id       INT UNSIGNED,
    character_name  VARCHAR(100) NOT NULL,
    description     TEXT,
    level           INT NOT NULL DEFAULT 1,
    strength        INT NOT NULL,
    dexterity       INT NOT NULL,
    constitution    INT NOT NULL,
    intelligence    INT NOT NULL,
    wisdom          INT NOT NULL,
    charisma        INT NOT NULL,
    max_hp          INT NOT NULL,
    current_hp      INT NOT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (player_id) REFERENCES user (user_id) ON DELETE CASCADE,
    FOREIGN KEY (desk_id) REFERENCES desk (desk_id) ON DELETE CASCADE,
    FOREIGN KEY (class_id) REFERENCES classes (class_id) ON DELETE SET NULL,
    FOREIGN KEY (weapon_id) REFERENCES weapons (weapon_id) ON DELETE SET NULL
);

-- Piezas de armadura
CREATE TABLE armor_parts (
    armor_id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    armor_name    VARCHAR(50) NOT NULL,
    armor_slot    ENUM('head', 'body', 'legs', 'hands', 'feet', 'shield', 'full_body') NOT NULL,
    base_ac       INT NOT NULL,
    dex_bonus     BOOLEAN DEFAULT TRUE,
    max_dex_bonus INT DEFAULT NULL,
    description   TEXT
);

-- Armaduras equipadas
CREATE TABLE equipped_armor (
    character_id  INT UNSIGNED NOT NULL,
    armor_id      INT UNSIGNED NOT NULL,
    equipped_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (character_id, armor_id),
    FOREIGN KEY (character_id) REFERENCES player_character(character_id) ON DELETE CASCADE,
    FOREIGN KEY (armor_id) REFERENCES armor_parts(armor_id) ON DELETE CASCADE
);

-- Hechizos
CREATE TABLE spells (
    spell_id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    spell_name  VARCHAR(100) NOT NULL,
    spell_level INT NOT NULL,
    description TEXT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Magias
CREATE TABLE magic (
    magic_id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    magic_name  VARCHAR(100) NOT NULL,
    spell_level INT NOT NULL,
    description TEXT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Efectos o Estados
CREATE TABLE status_effects (
    effect_id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    effect_name   VARCHAR(50) NOT NULL UNIQUE,
    description   TEXT,
    duration      INT NOT NULL, -- duración en turnos o segundos
    effect_type   ENUM('damage_over_time', 'slow', 'buff', 'debuff', 'stun', 'heal_over_time', 'other') NOT NULL
);

-- Efectos activos en personajes
CREATE TABLE character_status_effects (
    character_id  INT UNSIGNED NOT NULL,
    effect_id     INT UNSIGNED NOT NULL,
    turns_left    INT NOT NULL, -- cuántos turnos quedan
    intensity     INT DEFAULT 1, -- opcional para escalas o stacks
    applied_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (character_id, effect_id),
    FOREIGN KEY (character_id) REFERENCES player_character(character_id) ON DELETE CASCADE,
    FOREIGN KEY (effect_id) REFERENCES status_effects(effect_id) ON DELETE CASCADE
);

-- Relación entre magias y efectos
CREATE TABLE magic_effects (
    magic_id     INT UNSIGNED NOT NULL,
    effect_id    INT UNSIGNED NOT NULL,
    chance      DECIMAL(5,4) DEFAULT 1.0, -- probabilidad de aplicar el efecto (0 a 1)
    intensity   INT DEFAULT 1, -- potencia del efecto
    PRIMARY KEY (magic_id, effect_id),
    FOREIGN KEY (magic_id) REFERENCES magic(magic_id) ON DELETE CASCADE,
    FOREIGN KEY (effect_id) REFERENCES status_effects(effect_id) ON DELETE CASCADE
);

-- Ítems
CREATE TABLE items (
    item_id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    item_name   VARCHAR(100) NOT NULL,
    description TEXT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ### INVENTARIO ###--

-- Inventario de armas
CREATE TABLE inventory_weapons (
    inventory_id  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    character_id  INT UNSIGNED NOT NULL,
    weapon_id     INT UNSIGNED NOT NULL,
    quantity      INT NOT NULL DEFAULT 1,
    equipped      BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (character_id) REFERENCES player_character(character_id) ON DELETE CASCADE,
    FOREIGN KEY (weapon_id) REFERENCES weapons(weapon_id) ON DELETE CASCADE
);

-- Inventario de armaduras
CREATE TABLE inventory_armor (
    inventory_id  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    character_id  INT UNSIGNED NOT NULL,
    armor_id      INT UNSIGNED NOT NULL,
    quantity      INT NOT NULL DEFAULT 1,
    equipped      BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (character_id) REFERENCES player_character(character_id) ON DELETE CASCADE,
    FOREIGN KEY (armor_id) REFERENCES armor_parts(armor_id) ON DELETE CASCADE
);

-- Inventario de magias
CREATE TABLE inventory_magic (
    inventory_id  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    character_id  INT UNSIGNED NOT NULL,
    magic_id      INT UNSIGNED NOT NULL,
    quantity      INT NOT NULL DEFAULT 1,
    FOREIGN KEY (character_id) REFERENCES player_character(character_id) ON DELETE CASCADE,
    FOREIGN KEY (magic_id) REFERENCES magic(magic_id) ON DELETE CASCADE
);

-- Inventario de ítems generales
CREATE TABLE inventory_items (
    inventory_id  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    character_id  INT UNSIGNED NOT NULL,
    item_id       INT UNSIGNED NOT NULL,
    quantity      INT NOT NULL DEFAULT 1,
    FOREIGN KEY (character_id) REFERENCES player_character(character_id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES items(item_id) ON DELETE CASCADE
);
