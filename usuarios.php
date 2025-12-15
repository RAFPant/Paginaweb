<?php
include("config/conexion.php");
require_once "auth.php";
require_login();
if(!is_admin()){ die("Acceso denegado"); }

// Crear usuario
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['usuario'])){
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];
    $rol = $_POST['rol'] === 'admin' ? 'admin' : 'encuestador';
    // Hashear con password_hash (mejor). Si quieres mantener compatibilidad con md5, no lo uses.
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO usuarios (usuario, password, rol) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $usuario, $hash, $rol);
    $stmt->execute();
    header("Location: usuarios.php");
    exit;
}

$u = $conn->query("SELECT id, usuario, rol, fecha_creacion FROM usuarios ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="utf-8"><title>Usuarios</title></head>
<body>
<a href="index.php">← Volver</a>
<h1>Usuarios</h1>
<h2>Crear usuario</h2>
<form method="POST">
    <label>Usuario</label><input name="usuario" required>
    <label>Contraseña</label><input name="password" required type="password">
    <label>Rol</label>
    <select name="rol">
        <option value="encuestador">Encuestador</option>
        <option value="admin">Administrador</option>
    </select>
    <button>Crear</button>
</form>

<h2>Lista</h2>
<table border="1" cellpadding="6">
<tr><th>ID</th><th>Usuario</th><th>Rol</th><th>Creado</th></tr>
<?php while($row = $u->fetch_assoc()): ?>
<tr>
    <td><?php echo $row['id']; ?></td>
    <td><?php echo htmlentities($row['usuario']); ?></td>
    <td><?php echo $row['rol']; ?></td>
    <td><?php echo $row['fecha_creacion']; ?></td>
</tr>
<?php endwhile; ?>
</table>
</body>
</html>
