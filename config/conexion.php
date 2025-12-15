<?php
// encuestas_api/config/conexion.php
// Propósito: conexión a la base de datos para los endpoints de la API. Define las
// credenciales y crea la conexión mysqli en la variable `$conexion` (nótese la
// diferencia con `$conn` usado en el root de la app). Incluir con
// `require_once 'config/conexion.php'` desde los archivos de `encuestas_api/`.
// NOTA: mantener credenciales fuera del repo para entornos de producción.

$host = "localhost";
$user = "root";
$pass = "";
$db   = "encuesta_db";

$conexion = new mysqli($host, $user, $pass, $db);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>

