<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once "config/conexion.php"; // <-- Ajusta tu conexión

$query = "
    SELECT 
        p.id,
        p.texto,
        p.tipo,
        p.id_dimension,
        d.nombre AS dimension
    FROM preguntas p
    INNER JOIN dimensiones d ON d.id = p.id_dimension
    WHERE p.activa = 1
    ORDER BY p.id_dimension, p.id
";

$result = $conexion->query($query);
$preguntas = [];

while ($row = $result->fetch_assoc()) {
    $opciones = [];
    if ($row["tipo"] == "cerrada") {
        $resOpc = $conexion->query("
            SELECT id, texto AS texto_opcion
            FROM opciones_respuesta
            WHERE id_pregunta = {$row["id"]}
            ORDER BY id ASC
        ");
        while ($opc = $resOpc->fetch_assoc()) {
            $opciones[] = $opc;
        }
    }

    $preguntas[] = [
        "id" => intval($row["id"]),
        "texto" => $row["texto"],
        "tipo" => $row["tipo"],
        "id_dimension" => intval($row["id_dimension"]),
        "dimension" => $row["dimension"],
        "opciones" => $opciones
    ];
}

echo json_encode($preguntas, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
