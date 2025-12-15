<?php
session_start();
include("config/conexion.php");

// Verificar sesión y rol
if(!isset($_SESSION['id']) || $_SESSION['rol'] != 'encuestador'){
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Preguntas - Encuestador</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Gestión de Preguntas - Encuestador</h1>

<!-- Botones principales -->
<div class="botonera">
    <!-- Botón para crear preguntas usando el banco -->
    <a href="creacion_desde_banco.php" class="btn btn-exportar">➕ Crear Pregunta usando Banco</a>
    <a href="buscar_productor.php" class="btn btn-exportar">🔎 Buscar Productor</a>
    <a href="logout.php" class="btn btn-eliminar">Cerrar sesión</a>
</div>

<!-- Formulario para agregar preguntas nuevas directamente -->
<form action="preguntas/agregar.php" method="POST">
    <label>Texto de la pregunta:</label>
    <textarea name="texto" required></textarea>

    <label>Tipo de pregunta:</label>
    <select name="tipo" id="tipo" onchange="mostrarOpciones()">
        <option value="abierta">Abierta</option>
        <option value="cerrada">Cerrada (opción múltiple)</option>
    </select>

    <div id="opciones" style="display:none;">
        <label>Opciones de respuesta:</label>
        <div id="contenedor-opciones" class="opciones-container">
            <input type="text" name="opciones[]" placeholder="Opción 1">
        </div>
        <button type="button" class="boton-agregar" onclick="agregarOpcion()">Agregar otra opción</button>
    </div>

    <label>Dimensión:</label>
    <select name="id_dimension" required>
        <?php
        $dim = $conn->query("SELECT * FROM dimensiones");
        while($d = $dim->fetch_assoc()){
            echo "<option value='".$d['id']."'>".$d['nombre']."</option>";
        }
        ?>
    </select>

    <button type="submit" class="btn btn-editar">Guardar pregunta</button>
</form>

<!-- Listado de preguntas -->
<h2>Listado de preguntas</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Texto</th>
        <th>Tipo</th>
        <th>Dimensión</th>
        <th>Opciones</th>
        <th>Acciones</th>
    </tr>
    <?php
    $sql = "SELECT p.*, d.nombre AS dimension FROM preguntas p JOIN dimensiones d ON p.id_dimension = d.id";
    $preguntas = $conn->query($sql);
    while($p = $preguntas->fetch_assoc()){
        echo "<tr>
                <td>".$p['id']."</td>
                <td>".$p['texto']."</td>
                <td>".$p['tipo']."</td>
                <td>".$p['dimension']."</td>
                <td>";
        if($p['tipo'] == 'cerrada'){
            $op = $conn->query("SELECT texto FROM opciones_respuesta WHERE id_pregunta=".$p['id']);
            while($o = $op->fetch_assoc()){
                echo "- ".$o['texto']."<br>";
            }
        } else {
            echo "<i>No aplica</i>";
        }
        echo "</td>
              <td>
                <a class='btn btn-editar' href='preguntas/editar.php?id=".$p['id']."'>Editar</a>
                <a class='btn btn-eliminar' href='preguntas/eliminar.php?id=".$p['id']."' onclick='return confirm(\"¿Eliminar pregunta?\")'>Eliminar</a>
              </td>
            </tr>";
    }
    ?>
</table>

</body>
</html>
