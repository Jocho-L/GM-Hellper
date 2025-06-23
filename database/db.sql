CREATE DATABASE GM_Helper;

USE GM_Helper;

CREATE TABLE user (
    user_id     INT AUTO_INCREMENT PRIMARY KEY,
    username    VARCHAR(50) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    email       VARCHAR(100) NOT NULL UNIQUE,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE desk (
    desk_id     INT AUTO_INCREMENT PRIMARY KEY,
    desk_name   VARCHAR(100) NOT NULL,
    description_desk TEXT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_GM       INT,
    FOREIGN KEY (id_GM) REFERENCES user (user_id) ON DELETE SET NULL
);
CREATE TABLE classes (
    class_id    INT AUTO_INCREMENT PRIMARY KEY,
    class_name  VARCHAR(50) NOT NULL UNIQUE,
    hit_dice    VARCHAR(5) NOT NULL -- Ej: 'd6', 'd8', 'd10', 'd12'
);

CREATE TABLE player_character (
    character_id    INT AUTO_INCREMENT PRIMARY KEY,
    player_id       INT,
    desk_id         INT,
    class_id        INT,
    hit_dice        VARCHAR(10) NOT NULL, -- ej: 'd10', 'd6'
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
    FOREIGN KEY (class_id) REFERENCES classes (class_id) ON DELETE SET NULL
);


CREATE TABLE npc (
    npc_id    INT AUTO_INCREMENT PRIMARY KEY,
    desk_id         INT,
    npc_name  VARCHAR(100) NOT NULL,
    description     TEXT,
    level           INT NOT NULL DEFAULT 1,
    strength        INT NOT NULL,
    dexterity       INT NOT NULL,   --Destreza
    constitution    INT NOT NULL,
    intelligence    INT NOT NULL,
    wisdom          INT NOT NULL,  --Sabiduría
    charisma        INT NOT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (desk_id) REFERENCES desk (desk_id) ON DELETE CASCADE
);

CREATE TABLE weapons (
    weapon_id   INT AUTO_INCREMENT PRIMARY KEY,
    weapon_name VARCHAR(100) NOT NULL,
    dice_sides  INT NOT NULL,
    dice_count  INT NOT NULL,
    description TEXT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE magic (
    magic_id    INT AUTO_INCREMENT PRIMARY KEY,
    magic_name  VARCHAR(100) NOT NULL,
    spell_level INT NOT NULL,
    description TEXT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE items (
    item_id     INT AUTO_INCREMENT PRIMARY KEY,
    item_name   VARCHAR(100) NOT NULL,
    description TEXT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);