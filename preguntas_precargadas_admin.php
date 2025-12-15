<?php
session_start();
include("config/conexion.php");

// Verificar que sea admin
if(!isset($_SESSION['id']) || $_SESSION['rol'] != 'admin'){
    header("Location: login.php");
    exit;
}

$mensaje = "";

// Crear pregunta precargada
if(isset($_POST['crear'])){
    $texto = $_POST['texto'];
    $id_dimension = intval($_POST['id_dimension']);

    $stmt = $conn->prepare("INSERT INTO preguntas_precargadas (texto, id_dimension) VALUES (?, ?)");
    $stmt->bind_param("si", $texto, $id_dimension);
    if($stmt->execute()){
        $mensaje = "Pregunta precargada creada correctamente.";
    } else {
        $mensaje = "Error al crear pregunta: " . $conn->error;
    }
    $stmt->close();
}

// Eliminar pregunta precargada
if(isset($_GET['eliminar_id'])){
    $id = intval($_GET['eliminar_id']);
    $stmt = $conn->prepare("DELETE FROM preguntas_precargadas WHERE id=?");
    $stmt->bind_param("i", $id);
    if($stmt->execute()){
        $mensaje = "Pregunta precargada eliminada correctamente.";
    } else {
        $mensaje = "Error al eliminar pregunta: " . $conn->error;
    }
    $stmt->close();
}

// Consultar preguntas precargadas
$resultado = $conn->query("SELECT p.*, d.nombre AS dimension FROM preguntas_precargadas p JOIN dimensiones d ON p.id_dimension=d.id ORDER BY p.id ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Banco de Preguntas Precargadas - Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Banco de Preguntas Precargadas</h1>

<?php if($mensaje) echo "<p style='color:green;'>$mensaje</p>"; ?>

<!-- Formulario Crear Pregunta -->
<h2>Crear Nueva Pregunta Precargada</h2>
<form method="POST">
    <input type="text" name="texto" placeholder="Texto de la pregunta" required>
    <label>Dimensión:</label>
    <select name="id_dimension" required>
        <?php
        $dimensiones = $conn->query("SELECT * FROM dimensiones ORDER BY nombre ASC");
        while($d = $dimensiones->fetch_assoc()){
            echo "<option value='".$d['id']."'>".$d['nombre']."</option>";
        }
        ?>
    </select>
    <button type="submit" name="crear" class="btn btn-exportar">Guardar pregunta</button>
</form>

<!-- Listado de preguntas precargadas -->
<h2>Listado de Preguntas Precargadas</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Pregunta</th>
        <th>Dimensión</th>
        <th>Acciones</th>
    </tr>
    <?php while($row = $resultado->fetch_assoc()): ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo htmlspecialchars($row['texto']); ?></td>
        <td><?php echo htmlspecialchars($row['dimension']); ?></td>
        <td>
            <a href="preguntas_precargadas_admin.php?eliminar_id=<?php echo $row['id']; ?>" 
               class="btn btn-eliminar" 
               onclick="return confirm('¿Eliminar pregunta precargada?');">Eliminar</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<p><a href="index_admin.php" class="btn btn-exportar">⬅ Volver al Panel de Admin</a></p>

</body>
</html>
