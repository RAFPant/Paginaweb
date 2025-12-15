<?php
include("config/conexion.php");
require_once "auth.php";
require_login();
if(!is_admin()){ die("Acceso denegado"); }

$id = intval($_GET['id'] ?? 0);
if($id <= 0) die("ID inválido");

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $nombre = $_POST['nombre'];
    $desc = $_POST['descripcion'] ?? '';
    $act = isset($_POST['activa']) ? 1 : 0;
    $stmt = $conn->prepare("UPDATE dimensiones SET nombre=?, descripcion=?, activa=? WHERE id=?");
    $stmt->bind_param("ssii",$nombre,$desc,$act,$id);
    $stmt->execute();
    header("Location: dimensiones.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM dimensiones WHERE id=?");
$stmt->bind_param("i",$id);
$stmt->execute();
$res = $stmt->get_result();
$d = $res->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="utf-8"><title>Editar dimensión</title></head>
<body>
<a href="dimensiones.php">← Volver</a>
<h1>Editar dimensión #<?php echo $id; ?></h1>
<form method="POST">
    <label>Nombre</label>
    <input name="nombre" required value="<?php echo htmlentities($d['nombre']); ?>">
    <label>Descripción</label>
    <textarea name="descripcion"><?php echo htmlentities($d['descripcion']); ?></textarea>
    <label><input type="checkbox" name="activa" <?php echo $d['activa'] ? 'checked' : ''; ?>> Activa</label>
    <button>Guardar</button>
</form>
</body>
</html>
