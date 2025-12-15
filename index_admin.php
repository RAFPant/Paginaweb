<?php
session_start();
include("config/conexion.php");

// Verificar sesión y rol
if(!isset($_SESSION['id']) || $_SESSION['rol'] != 'admin'){
    header("Location: login.php");
    exit;
}

// Mensaje de éxito/error
$mensaje = "";

// =====================
// GESTIÓN DE ENCUESTADORES
// =====================
if(isset($_POST['crear_encuestador'])){
    $usuario = $_POST['usuario'];
    $password = md5($_POST['password']); // Ajusta según tu sistema de passwords
    $rol = 'encuestador';

    $stmt = $conn->prepare("INSERT INTO usuarios (usuario, password, rol) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $usuario, $password, $rol);
    if($stmt->execute()){
        $mensaje = "Encuestador creado correctamente.";
    } else {
        $mensaje = "Error al crear encuestador: " . $conn->error;
    }
    $stmt->close();
}

if(isset($_GET['eliminar_encuestador_id'])){
    $id = intval($_GET['eliminar_encuestador_id']);
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id=? AND rol='encuestador'");
    $stmt->bind_param("i", $id);
    if($stmt->execute()){
        $mensaje = "Encuestador eliminado correctamente.";
    } else {
        $mensaje = "Error al eliminar encuestador: " . $conn->error;
    }
    $stmt->close();
}

// Obtener lista de encuestadores
$encuestadores = $conn->query("SELECT * FROM usuarios WHERE rol='encuestador'");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Preguntas - Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Gestión de Preguntas - Admin</h1>

<?php if($mensaje) echo "<p style='color:green;'>$mensaje</p>"; ?>

<!-- BOTONERA PRINCIPAL -->
<div class="botonera">
    <a href="dimensiones_admin.php" class="btn btn-exportar">Dimensiones</a>
    <a href="preguntas_precargadas_admin.php" class="btn btn-exportar">🏦 Banco de Preguntas Precargadas</a>
    <a href="creacion_desde_banco.php" class="btn btn-exportar">➕ Crear Pregunta usando Banco</a>
    <a href="buscar_productor.php" class="btn btn-editar">🔍 Buscar Productor</a>
    <a href="logout.php" class="btn btn-eliminar">Cerrar sesión</a>
</div>

<!-- FORMULARIO CREAR ENCUESTADOR -->
<h2>Crear Nuevo Encuestador</h2>
<form method="POST">
    <input type="text" name="usuario" placeholder="Usuario" required>
    <input type="password" name="password" placeholder="Contraseña" required>
    <button type="submit" name="crear_encuestador" class="btn btn-editar">Crear Encuestador</button>
</form>

<!-- LISTADO ENCUESTADORES -->
<h2>Encuestadores Existentes</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Usuario</th>
        <th>Acciones</th>
    </tr>
    <?php while($row = $encuestadores->fetch_assoc()): ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo htmlspecialchars($row['usuario']); ?></td>
        <td>
            <a href="index_admin.php?eliminar_encuestador_id=<?php echo $row['id']; ?>" 
               class="btn btn-eliminar" 
               onclick="return confirm('¿Eliminar este encuestador?');">Eliminar</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<!-- FORMULARIO PARA AGREGAR PREGUNTAS DIRECTAMENTE -->
<h2>Agregar Preguntas</h2>
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

<!-- LISTADO DE PREGUNTAS -->
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

<script>
function mostrarOpciones() {
    document.getElementById("opciones").style.display = (document.getElementById("tipo").value === "cerrada") ? "block" : "none";
}

function agregarOpcion() {
    var contenedor = document.getElementById("contenedor-opciones");
    var input = document.createElement("input");
    input.type = "text";
    input.name = "opciones[]";
    input.placeholder = "Nueva opción";
    contenedor.appendChild(document.createElement("br"));
    contenedor.appendChild(input);
}
</script>

</body>
</html>
