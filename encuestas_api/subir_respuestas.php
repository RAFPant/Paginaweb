<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=UTF-8");

require_once "../confi/conexion.php";

$json = file_get_contents("php://input");
$data = json_decode($json, true);

if (!$data || !is_array($data)) {
    echo json_encode(["success" => false, "error" => "JSON inválido"]);
    exit;
}

$errores = [];
$insertadas = 0;

foreach ($data as $resp) {

    if (!isset($resp["id_encuesta_pregunta"])) continue;

    $query = "
        INSERT INTO respuestas (id_encuesta_pregunta, respuesta_texto, id_opcion)
        VALUES (?, ?, ?)
    ";

    $stmt = $conexion->prepare($query);
    $txt = $resp["respuesta_texto"] ?? null;
    $op = $resp["id_opcion"] ?? null;

    $stmt->bind_param("isi", $resp["id_encuesta_pregunta"], $txt, $op);

    if ($stmt->execute()) {
        $insertadas++;
    } else {
        $errores[] = $resp["id_encuesta_pregunta"];
    }

    $stmt->close();
}

echo json_encode([
    "success" => true,
    "insertadas" => $insertadas,
    "errores" => $errores
]);
?>
