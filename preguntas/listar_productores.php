<?php
require_once "../config/conexiones.php";

// Obtener lista de productores
$sql = "SELECT id, nombre_productor FROM productores ORDER BY nombre_productor ASC";
$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Productores</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        th { background: #f3f3f3; }
        a.boton {
            padding: 6px 12px;
            background: #2979ff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        a.boton.eliminar { background: #d32f2f; }
        a.boton.exportar { background: #388e3c; }
    </style>
</head>
<body>

<h2>Lista de Productores</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Nombre del Productor</th>
        <th>Acciones</th>
    </tr>

    <?php while ($row = $resultado->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['nombre_productor']; ?></td>
            <td>
                <a class="boton" href="ver_respuestas.php?id=<?php echo $row['id']; ?>">Ver respuestas</a>

                <a class="boton exportar" href="../exportar/exportar_respuestas.php?id=<?php echo $row['id']; ?>">
                    Exportar respuestas
                </a>

                <a class="boton eliminar" 
                   href="eliminar_cuestionario.php?id=<?php echo $row['id']; ?>"
                   onclick="return confirm('¿Seguro que deseas eliminar el cuestionario de este productor?');">
                   Eliminar cuestionario
                </a>
            </td>
        </tr>
    <?php } ?>
</table>

</body>
</html>
