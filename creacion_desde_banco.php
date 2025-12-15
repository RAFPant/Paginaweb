<?php
session_start();
include("config/conexion.php");

// Verificar sesión
if(!isset($_SESSION['id'])){
    header("Location: login.php");
    exit;
}

// Variables
$mensaje = "";

// Crear pregunta desde precargada
if(isset($_POST['crear'])){
    $id_precargada = intval($_POST['id_precargada']);
    $id_dimension = intval($_POST['id_dimension']);
    $tipo = $_POST['tipo'];
    
    // Crear pregunta oficial
    $stmt = $conn->prepare("INSERT INTO preguntas (texto, tipo, id_dimension) 
                            SELECT texto, ?, ? FROM preguntas_precargadas WHERE id=?");
    $stmt->bind_param("sii", $tipo, $id_dimension, $id_precargada);
    if($stmt->execute()){
        $mensaje = "Pregunta creada desde precargada correctamente.";
    } else {
        $mensaje = "Error al crear pregunta: " . $conn->error;
    }
    $stmt->close();
}

// Obtener todas las dimensiones
$dimensiones = $conn->query("SELECT * FROM dimensiones ORDER BY nombre ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Pregunta desde Banco</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Crear Pregunta desde Banco de Preguntas Precargadas</h1>

<?php if($mensaje) echo "<p style='color:green;'>$mensaje</p>"; ?>

<form method="POST">
    <label>Dimensión:</label>
    <select name="id_dimension" id="id_dimension" onchange="cargarPrecargadas()" required>
        <option value="">-- Selecciona una dimensión --</option>
        <?php while($d = $dimensiones->fetch_assoc()): ?>
        <option value="<?php echo $d['id']; ?>"><?php echo htmlspecialchars($d['nombre']); ?></option>
        <?php endwhile; ?>
    </select>

    <label>Preguntas Precargadas:</label>
    <select name="id_precargada" id="precargadas" required>
        <option value="">-- Selecciona una pregunta --</option>
    </select>

    <label>Tipo de pregunta:</label>
    <select name="tipo" required>
        <option value="abierta">Abierta</option>
        <option value="cerrada">Cerrada</option>
    </select>

    <button type="submit" name="crear" class="btn btn-exportar">Crear Pregunta</button>
</form>

<p><a href="<?php echo ($_SESSION['rol']=='admin')?'index_admin.php':'index_encuestador.php'; ?>" class="btn btn-exportar">⬅ Volver</a></p>

<script>
function cargarPrecargadas(){
    const dimId = document.getElementById('id_dimension').value;
    const select = document.getElementById('precargadas');
    select.innerHTML = '<option value="">Cargando...</option>';

    fetch('obtener_precargadas.php?dimension_id=' + dimId)
        .then(response => response.json())
        .then(data => {
            select.innerHTML = '<option value="">-- Selecciona una pregunta --</option>';
            data.forEach(p => {
                const option = document.createElement('option');
                option.value = p.id;
                option.textContent = p.texto;
                select.appendChild(option);
            });
        });
}
</script>

</body>
</html>
