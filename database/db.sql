
CREATE DATABASE GM_Helper;
USE GM_Helper;

CREATE TABLE user (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE desk (
    desk_id INT AUTO_INCREMENT PRIMARY KEY,
    desk_name VARCHAR(100) NOT NULL,
    description_desk TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_GM INT,
    FOREIGN KEY (id_GM) REFERENCES user(user_id) ON DELETE SET NULL
);

CREATE TABLE player_character(
    character_id INT AUTO_INCREMENT PRIMARY KEY,
    character_name VARCHAR(100) NOT NULL,
    player_id INT,|
    FOREIGN KEY (player_id) REFERENCES user(user_id) ON DELETE CASCADE
);

CREATE TABLE npc(

);

CREATE TABLE weapons(
    weapon_id INT AUTO_INCREMENT PRIMARY KEY,
    weapon_name VARCHAR(100) NOT NULL,
    dice_sides INT NOT NULL,
    dice_count INT NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE magic (
);

CREATE TABLE magic (
);