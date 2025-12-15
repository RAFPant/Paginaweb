<?php
session_start();
include("config/conexion.php");

// Verificar que el usuario esté logueado
if(!isset($_SESSION['id'])){
    header("Location: login.php");
    exit;
}

// Función para eliminar toda la información de un productor
if(isset($_GET['eliminar_id'])){
    $id_productor = intval($_GET['eliminar_id']);

    // Eliminar respuestas
    $stmt = $conn->prepare("DELETE r FROM respuestas r INNER JOIN encuestas_preguntas ep ON r.id_encuesta_pregunta = ep.id WHERE r.id_productor=?");
    $stmt->bind_param("i", $id_productor);
    $stmt->execute();
    $stmt->close();

    // Eliminar encuestas
    $stmt = $conn->prepare("DELETE FROM encuestas WHERE id_productor=?");
    $stmt->bind_param("i", $id_productor);
    $stmt->execute();
    $stmt->close();

    // Finalmente eliminar productor
    $stmt = $conn->prepare("DELETE FROM productores WHERE id=?");
    $stmt->bind_param("i", $id_productor);
    $stmt->execute();
    $stmt->close();

    $mensaje = "Productor eliminado completamente.";
}

// Búsqueda de productores
$busqueda = "";
if(isset($_POST['buscar'])){
    $busqueda = $_POST['buscar'];
    $sql = $conn->prepare("SELECT * FROM productores WHERE nombre_productor LIKE ?");
    $like = "%".$busqueda."%";
    $sql->bind_param("s", $like);
} else {
    $sql = $conn->prepare("SELECT * FROM productores");
}
$sql->execute();
$resultado = $sql->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Buscar Productor</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Buscar Productor</h1>

    <?php if(isset($mensaje)) echo "<p style='color:green;'>$mensaje</p>"; ?>

    <form method="POST">
        <input type="text" name="buscar" placeholder="Nombre del productor" value="<?php echo htmlspecialchars($busqueda); ?>">
        <button type="submit">Buscar</button>
    </form>

    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Edad</th>
            <th>Sexo</th>
            <th>Acciones</th>
        </tr>
        <?php while($row = $resultado->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['nombre_productor']); ?></td>
            <td><?php echo $row['edad']; ?></td>
            <td><?php echo $row['sexo']; ?></td>
            <td>
                <a href="ver_encuesta.php?id=<?php echo $row['id']; ?>">Ver Encuesta</a> | 
                <a href="buscar_productor.php?eliminar_id=<?php echo $row['id']; ?>" onclick="return confirm('¿Estás seguro de eliminar este productor y toda su información?');">Eliminar</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <!-- Botón para regresar al índice según rol -->
    <?php if($_SESSION['rol'] == 'admin'): ?>
        <p><a href="index_admin.php">Volver al Panel de Admin</a></p>
    <?php else: ?>
        <p><a href="index_encuestador.php">Volver al Panel de Encuestador</a></p>
    <?php endif; ?>

</body>
</html>
