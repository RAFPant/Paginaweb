<?php
session_start();
require_once "config/conexion.php";

if (!isset($_POST['usuario']) || !isset($_POST['password'])) {
    echo "Faltan datos";
    exit;
}

$usuario = trim($_POST['usuario']);
$password = trim($_POST['password']);

$sql = "SELECT * FROM usuarios WHERE usuario = '$usuario' AND password = '$password'";
$result = $conexion->query($sql);

if (!$result) {
    echo "Error en la consulta: " . $conexion->error;
    exit;
}

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();

    $_SESSION['usuario'] = $row['usuario'];
    $_SESSION['rol'] = $row['rol'];
    $_SESSION['id'] = $row['id'];

    if ($row['rol'] === "admin") {
        header("Location: index_admin.php");
        exit;
    } else if ($row['rol'] === "encuestador") {
        header("Location: index_encuestador.php");
        exit;
    } else {
        echo "Rol no válido";
        exit;
    }
} else {
    echo "Datos incorrectos";
    exit;
}
?>
