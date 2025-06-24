<?php
require_once __DIR__ . '/Conexion.php';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
?>