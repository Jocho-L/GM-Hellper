<?php
require_once __DIR__ . '/Conexion.php';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // En lugar de 'die', lanzamos la excepción para que el controlador la capture.
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}
?>