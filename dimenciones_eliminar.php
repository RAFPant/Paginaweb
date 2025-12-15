<?php
include("config/conexion.php");
require_once "auth.php";
require_login();
if(!is_admin()){ die("Acceso denegado"); }
$id = intval($_GET['id'] ?? 0);
if($id <= 0) header("Location: dimensiones.php");

# Preferible marcar inactiva en lugar de borrar para no romper FK
$stmt = $conn->prepare("UPDATE dimensiones SET activa = 0 WHERE id = ?");
$stmt->bind_param("i",$id);
$stmt->execute();
header("Location: dimensiones.php");
exit;
