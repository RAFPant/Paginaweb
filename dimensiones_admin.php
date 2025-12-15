<?php
session_start();
include("config/conexion.php");

// Verificar sesión y rol
if(!isset($_SESSION['id']) || $_SESSION['rol'] != 'admin'){
    header("Location: login.php");
    exit;
}

$mensaje = "";

// Crear dimensión
if(isset($_POST['crear'])){
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];

    $stmt = $conn->prepare("INSERT INTO dimensiones (nombre, descripcion) VALUES (?, ?)");
    $stmt->bind_param("ss", $nombre, $descripcion);
    if($stmt->execute()){
        $mensaje = "Dimensión creada correctamente.";
    } else {
        $mensaje = "Error al crear dimensión: " . $conn->error;
    }
    $stmt->close();
}

// Editar dimensión
if(isset($_POST['editar'])){
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];

    $stmt = $conn->prepare("UPDATE dimensiones SET nombre=?, descripcion=? WHERE id=?");
    $stmt->bind_param("ssi", $nombre, $descripcion, $id);
    if($stmt->execute()){
        $mensaje = "Dimensión actualizada correctamente.";
    } else {
        $mensaje = "Error al actualizar dimensión: " . $conn->error;
    }
    $stmt->close();
}

// Eliminar dimensión y todo lo relacionado
if(isset($_GET['eliminar_id'])){
    $id = intval($_GET['eliminar_id']);
    if($id > 0){
        $conn->begin_transaction();
        try {
            // Obtener preguntas de la dimensión
            $preguntas = $conn->query("SELECT id FROM preguntas WHERE id_dimension = $id");
            while($p = $preguntas->fetch_assoc()){
                $id_pregunta = $p['id'];
                $conn->query("DELETE FROM respuestas WHERE id_encuesta_pregunta IN (SELECT id FROM encuestas_preguntas WHERE id_pregunta = $id_pregunta)");
                $conn->query("DELETE FROM encuestas_preguntas WHERE id_pregunta = $id_pregunta");
                $conn->query("DELETE FROM opciones WHERE id_pregunta = $id_pregunta");
                $conn->query("DELETE FROM opciones_respuesta WHERE id_pregunta = $id_pregunta");
            }
            // Eliminar preguntas
            $conn->query("DELETE FROM preguntas WHERE id_dimension = $id");
            // Eliminar la dimensión
            $stmt = $conn->prepare("DELETE FROM dimensiones WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();

            $conn->commit();
            $mensaje = "Dimensión y todas sus preguntas eliminadas correctamente.";
        } catch(Exception $e){
            $conn->rollback();
            $mensaje = "Error al eliminar dimensión: " . $e->getMessage();
        }
    }
}

// Consultar todas las dimensiones
$resultado = $conn->query("SELECT * FROM dimensiones ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administrar Dimensiones</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Administrar Dimensiones</h1>

    <?php if($mensaje) echo "<p style='color:green;'>$mensaje</p>"; ?>

    <!-- Formulario Crear Dimensión -->
    <h2>Crear Nueva Dimensión</h2>
    <form method="POST">
        <input type="text" name="nombre" placeholder="Nombre" required>
        <input type="text" name="descripcion" placeholder="Descripción">
        <button type="submit" name="crear">Crear</button>
    </form>

    <!-- Listado de Dimensiones -->
    <h2>Dimensiones Existentes</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Acciones</th>
        </tr>
        <?php while($row = $resultado->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['nombre']); ?></td>
            <td><?php echo htmlspecialchars($row['descripcion']); ?></td>
            <td>
                <!-- Formulario editar en línea -->
                <form method="POST" style="display:inline-block;">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <input type="text" name="nombre" value="<?php echo htmlspecialchars($row['nombre']); ?>" required>
                    <input type="text" name="descripcion" value="<?php echo htmlspecialchars($row['descripcion']); ?>">
                    <button type="submit" name="editar">Editar</button>
                </form>
                <a href="dimensiones_admin.php?eliminar_id=<?php echo $row['id']; ?>" onclick="return confirm('¿Seguro que deseas eliminar esta dimensión una ves eliminada las preguntas tambien lo se borraran asi esta configurado?');">Eliminar</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <p><a href="index_admin.php">Volver al Panel de Admin</a></p>
</body>
</html>
