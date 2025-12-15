
<?php
require_once "../config/conexion.php";

// Obtener datos de la pregunta {"id":"58110","variant":"standard","title":"editar-pregunta-completo-con-opciones"}
if (isset($_GET["id"])) {
    $id = intval($_GET["id"]);

    $resultado = $conn->query("SELECT * FROM preguntas WHERE id = $id");
    $pregunta = $resultado->fetch_assoc();

    $dimensiones = $conn->query("SELECT * FROM dimensiones");

    // Obtener opciones si la pregunta es cerrada
    $opciones = [];
    if ($pregunta["tipo"] == "cerrada") {
        $op = $conn->query("SELECT * FROM opciones_respuesta WHERE id_pregunta = $id");
        while ($fila = $op->fetch_assoc()) {
            $opciones[] = $fila;
        }
    }
}

// Guardar cambios
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = $_POST["id"];
    $texto = $_POST["texto"];
    $tipo = $_POST["tipo"];
    $id_dimension = $_POST["id_dimension"];

    // Actualizar la pregunta
    $stmt = $conn->prepare("UPDATE preguntas SET texto=?, tipo=?, id_dimension=? WHERE id=?");
    $stmt->bind_param("ssii", $texto, $tipo, $id_dimension, $id);
    $stmt->execute();

    // Actualizar opciones (si aplica)
    if ($tipo == "cerrada") {

        // Eliminar opciones actuales
        $conn->query("DELETE FROM opciones_respuesta WHERE id_pregunta = $id");

        // Insertar nuevas opciones
        if (!empty($_POST["opciones"])) {
            foreach ($_POST["opciones"] as $opcion) {
                if (trim($opcion) != "") {
                    $opcion_txt = $conn->real_escape_string($opcion);
                    $conn->query("INSERT INTO opciones_respuesta (id_pregunta, texto) 
                                  VALUES ($id, '$opcion_txt')");
                }
            }
        }
    }

    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Pregunta</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        body { background:#f5f5f5; padding:20px; font-family:Arial; }
        form { background:white; padding:20px; border-radius:10px; max-width:700px; margin:auto; }
        input, textarea, select { width:100%; padding:10px; margin-bottom:12px; }
        .btn { padding:10px 15px; border-radius:5px; border:none; color:white; cursor:pointer; }
        .btn-guardar { background:#28a745; }
        .btn-agregar { background:#007bff; margin-bottom:10px; cursor:pointer; }
        .opcion-item { margin-bottom:8px; }
    </style>
</head>

<body>

<h1 style="text-align:center;">Editar Pregunta</h1>

<form method="POST">

    <input type="hidden" name="id" value="<?= $pregunta['id'] ?>">

    <label>Texto de la pregunta:</label>
    <textarea name="texto" required><?= htmlspecialchars($pregunta['texto']) ?></textarea>

    <label>Tipo:</label>
    <select name="tipo" id="tipo" onchange="mostrarOpciones()">
        <option value="abierta" <?= $pregunta['tipo']=='abierta'?'selected':'' ?>>Abierta</option>
        <option value="cerrada" <?= $pregunta['tipo']=='cerrada'?'selected':'' ?>>Cerrada</option>
    </select>

    <label>Dimensión:</label>
    <select name="id_dimension">
        <?php while($dim = $dimensiones->fetch_assoc()): ?>
            <option value="<?= $dim['id'] ?>" <?= $dim['id']==$pregunta['id_dimension']?'selected':'' ?>>
                <?= $dim['nombre'] ?>
            </option>
        <?php endwhile; ?>
    </select>

    <!-- Opciones dinámicas -->
    <div id="contenedorOpciones" style="display:<?= $pregunta['tipo']=='cerrada'?'block':'none' ?>;">
        <label>Opciones de respuesta:</label>

        <div id="opcionesLista">

            <?php if (!empty($opciones)): ?>
                <?php foreach ($opciones as $op): ?>
                    <div class="opcion-item">
                        <input type="text" name="opciones[]" value="<?= htmlspecialchars($op['texto']) ?>">
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="opcion-item">
                    <input type="text" name="opciones[]" placeholder="Opción 1">
                </div>
            <?php endif; ?>

        </div>

        <button type="button" class="btn btn-agregar" onclick="agregarOpcion()">Agregar opción</button>
    </div>

    <button type="submit" class="btn btn-guardar">Guardar cambios</button>
</form>

<div style="text-align:center; margin-top:20px;">
    <a href="../index.php">← Volver</a>
</div>

<script>
function mostrarOpciones() {
    let tipo = document.getElementById("tipo").value;
    document.getElementById("contenedorOpciones").style.display = (tipo === "cerrada") ? "block" : "none";
}

function agregarOpcion() {
    let lista = document.getElementById("opcionesLista");
    let div = document.createElement("div");
    div.className = "opcion-item";
    div.innerHTML = `<input type="text" name="opciones[]" placeholder="Nueva opción">`;
    lista.appendChild(div);
}
</script>

</body>
</html>
