<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Config\Database;

try {

    $database = new Database();
    $db = $database->connect();

    $stmt = $db->query("SELECT DATABASE() AS base_datos");

    $resultado = $stmt->fetch();

    echo "Conexión exitosa<br>";
    echo "Base de datos: " . $resultado['base_datos'];

} catch (PDOException $e) {

    echo "Error: " . $e->getMessage();

}