<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

require_once "../confi/conexion.php";

$json = file_get_contents("php://input");
$data = json_decode($json, true);

if (!$data || !isset($data["productor"]) || !isset($data["preguntas"])) {
    echo json_encode(["success" => false, "error" => "JSON inválido"]);
    exit;
}

$productor = $data["productor"];
$preguntas = $data["preguntas"];

// PASO 1: BUSCAR PRODUCTOR POR NOMBRE + FECHA
$stmt = $conexion->prepare("
    SELECT id 
    FROM productores 
    WHERE nombre_productor = ? AND fecha_aplicacion = ?
    LIMIT 1
");
$stmt->bind_param(
    "ss",
    $productor["nombre_productor"],
    $productor["fecha_aplicacion"]
);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($id_productor);
    $stmt->fetch();
    $stmt->close();
} else {

    // PASO 2: INSERTAR NUEVO PRODUCTOR
    $query = "
        INSERT INTO productores (
            estado, municipio, localidad,
            fecha_aplicacion, hora_aplicacion,
            nombre_encuestador, nombre_productor,
            sexo, edad, escolaridad,
            tiempo_dedicado_anios, num_personas_hogar,
            hombres_adultos, mujeres_adultas,
            ninos, ninas, tiene_telefono,
            tiene_radio, recibio_apoyo,
            apoyo_cual
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
    ";

    $stmt2 = $conexion->prepare($query);
    $stmt2->bind_param(
        "sssssssissiiiiiiiiiis",
        $productor["estado"],
        $productor["municipio"],
        $productor["localidad"],
        $productor["fecha_aplicacion"],
        $productor["hora_aplicacion"],
        $productor["nombre_encuestador"],
        $productor["nombre_productor"],
        $productor["sexo"],
        $productor["edad"],
        $productor["escolaridad"],
        $productor["tiempo_dedicado_anios"],
        $productor["num_personas_hogar"],
        $productor["hombres_adultos"],
        $productor["mujeres_adultas"],
        $productor["ninos"],
        $productor["ninas"],
        $productor["tiene_telefono"],
        $productor["tiene_radio"],
        $productor["recibio_apoyo"],
        $productor["apoyo_cual"]
    );

    if (!$stmt2->execute()) {
        echo json_encode(["success" => false, "error" => "Error al insertar productor"]);
        exit;
    }

    $id_productor = $stmt2->insert_id;
    $stmt2->close();
}

// PASO 3: CREAR ENCUESTA
$query3 = "INSERT INTO encuestas (id_productor) VALUES (?)";
$stmt3 = $conexion->prepare($query3);
$stmt3->bind_param("i", $id_productor);

if (!$stmt3->execute()) {
    echo json_encode(["success" => false, "error" => "Error al crear encuesta"]);
    exit;
}

$id_encuesta = $stmt3->insert_id;
$stmt3->close();

// PASO 4: GUARDAR PREGUNTAS + RESPUESTAS
foreach ($preguntas as $p) {

    // 1) Insertar encuestas_preguntas
    $query4 = "
        INSERT INTO encuestas_preguntas (
            id_encuesta, id_pregunta, texto_pregunta, tipo_pregunta, id_dimension
        ) VALUES (?,?,?,?,?)
    ";

    $stmt4 = $conexion->prepare($query4);
    $stmt4->bind_param(
        "iissi",
        $id_encuesta,
        $p["id_pregunta"],
        $p["texto_pregunta"],
        $p["tipo"],
        $p["id_dimension"]
    );

    if (!$stmt4->execute()) {
        echo json_encode(["success" => false, "error" => "Error al insertar encuestas_preguntas"]);
        exit;
    }

    $id_enc_preg = $stmt4->insert_id;
    $stmt4->close();

    // 2) Insertar respuesta
    $query5 = "
        INSERT INTO respuestas (id_encuesta_pregunta, respuesta_texto, id_opcion)
        VALUES (?,?,?)
    ";

    $stmt5 = $conexion->prepare($query5);
    $txt = $p["respuesta_texto"] ?? null;
    $op = $p["id_opcion"] ?? null;

    $stmt5->bind_param("isi", $id_enc_preg, $txt, $op);

    if (!$stmt5->execute()) {
        echo json_encode(["success" => false, "error" => "Error al insertar respuesta"]);
        exit;
    }
    $stmt5->close();
}

echo json_encode([
    "success" => true,
    "msg" => "Encuesta registrada correctamente",
    "id_productor" => $id_productor,
    "id_encuesta" => $id_encuesta
]);
?>
