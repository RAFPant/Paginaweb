<?php
include("config/conexion.php");
require_once "auth.php";
require_login();
if(!is_admin()){ die("Acceso denegado"); }

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'])){
    $nombre = $_POST['nombre'];
    $desc = $_POST['descripcion'] ?? '';
    $stmt = $conn->prepare("INSERT INTO dimensiones (nombre, descripcion, activa) VALUES (?, ?, 1)");
    $stmt->bind_param("ss", $nombre, $desc);
    $stmt->execute();
    header("Location: dimensiones.php");
    exit;
}

$dim = $conn->query("SELECT * FROM dimensiones ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="utf-8"><title>Dimensiones</title></head>
<body>
<h1>Dimensiones</h1>
<a href="index.php">← Volver</a>
<h2>Añadir dimensión</h2>
<form method="POST">
    <label>Nombre</label>
    <input name="nombre" required>
    <label>Descripción</label>
    <textarea name="descripcion"></textarea>
    <button>Añadir</button>
</form>

<h2>Lista</h2>
<table border="1" cellpadding="6">
    <tr><th>ID</th><th>Nombre</th><th>Activa</th><th>Acciones</th></tr>
    <?php while($d = $dim->fetch_assoc()): ?>
    <tr>
        <td><?php echo $d['id']; ?></td>
        <td><?php echo htmlentities($d['nombre']); ?></td>
        <td><?php echo $d['activa'] ? 'Sí' : 'No'; ?></td>
        <td>
            <a href="dimensiones_editar.php?id=<?php echo $d['id']; ?>">Editar</a>
            <a href="dimensiones_eliminar.php?id=<?php echo $d['id']; ?>" onclick="return confirm('Eliminar dimensión?')">Eliminar</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
</body>
</html>
