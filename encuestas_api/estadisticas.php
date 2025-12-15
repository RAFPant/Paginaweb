<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once "../confi/conexion.php";

$stats = [];

/* -----------------------------------------
   TOTAL PRODUCTORES
--------------------------------------------*/
$q1 = $conexion->query("SELECT COUNT(*) AS total FROM productores");
$stats["total_productores"] = $q1->fetch_assoc()["total"];

/* -----------------------------------------
   TOTAL ENCUESTAS
--------------------------------------------*/
$q2 = $conexion->query("SELECT COUNT(*) AS total FROM encuestas");
$stats["total_encuestas"] = $q2->fetch_assoc()["total"];

/* -----------------------------------------
   ENCUESTAS POR DÍA
--------------------------------------------*/
$q3 = $conexion->query("
    SELECT DATE(fecha_aplicacion) AS fecha, COUNT(*) AS cantidad
    FROM productores
    GROUP BY DATE(fecha_aplicacion)
    ORDER BY fecha DESC
");
$encuestas_dia = [];
while ($row = $q3->fetch_assoc()) {
    $encuestas_dia[] = $row;
}
$stats["encuestas_por_dia"] = $encuestas_dia;

/* -----------------------------------------
   PREGUNTAS RESPONDIDAS
--------------------------------------------*/
$q4 = $conexion->query("SELECT COUNT(*) AS total FROM respuestas");
$stats["total_respuestas"] = $q4->fetch_assoc()["total"];

/* -----------------------------------------
   SEXO (H/M/O)
--------------------------------------------*/
$q5 = $conexion->query("
    SELECT sexo, COUNT(*) AS total 
    FROM productores 
    GROUP BY sexo
");
$sexo = [];
while ($row = $q5->fetch_assoc()) {
    $sexo[] = $row;
}
$stats["sexo"] = $sexo;

/* -----------------------------------------
   ESCOLARIDAD MÁS COMÚN
--------------------------------------------*/
$q6 = $conexion->query("
    SELECT escolaridad, COUNT(*) AS total
    FROM productores
    GROUP BY escolaridad
    ORDER BY total DESC
");
$stats["escolaridad"] = [];
while ($row = $q6->fetch_assoc()) {
    $stats["escolaridad"][] = $row;
}

/* -----------------------------------------
   EDAD (PROMEDIO)
--------------------------------------------*/
$q7 = $conexion->query("
    SELECT AVG(edad) AS promedio FROM productores
");
$stats["promedio_edad"] = round($q7->fetch_assoc()["promedio"], 2);

/* -----------------------------------------
   PREGUNTAS POR DIMENSIÓN
--------------------------------------------*/
$q8 = $conexion->query("
    SELECT d.nombre AS dimension, COUNT(p.id) AS total
    FROM preguntas p
    INNER JOIN dimensiones d ON d.id = p.id_dimension
    GROUP BY p.id_dimension
");
$dim = [];
while ($row = $q8->fetch_assoc()) {
    $dim[] = $row;
}
$stats["preguntas_por_dimension"] = $dim;

echo json_encode([
    "success" => true,
    "estadisticas" => $stats
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
