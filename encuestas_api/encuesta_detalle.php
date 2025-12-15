<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once "../confi/conexion.php";

if (!isset($_GET["id_encuesta"])) {
    echo json_encode(["success" => false, "error" => "Falta id_encuesta"]);
    exit;
}

$id_encuesta = intval($_GET["id_encuesta"]);
$data = [];

/* -----------------------------------------
   DATOS DE LA ENCUESTA + PRODUCTOR
--------------------------------------------*/
$q1 = $conexion->prepare("
    SELECT 
        e.id AS id_encuesta,
        e.fecha_aplicacion,
        p.*
    FROM encuestas e
    INNER JOIN productores p ON p.id = e.id_productor
    WHERE e.id = ?
    LIMIT 1
");
$q1->bind_param("i", $id_encuesta);
$q1->execute();
$res1 = $q1->get_result();

if ($res1->num_rows == 0) {
    echo json_encode(["success" => false, "error" => "Encuesta no encontrada"]);
    exit;
}
$data["productor"] = $res1->fetch_assoc();

/* -----------------------------------------
   PREGUNTAS + RESPUESTAS
--------------------------------------------*/
$q2 = $conexion->prepare("
    SELECT 
        ep.id AS id_encuesta_pregunta,
        ep.texto_pregunta,
        ep.tipo_pregunta,
        ep.id_dimension,
        d.nombre AS dimension,
        r.respuesta_texto,
        r.id_opcion,
        o.texto_opcion
