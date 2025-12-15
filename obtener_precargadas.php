<?php
include("config/conexion.php");

$dimension_id = intval($_GET['dimension_id'] ?? 0);

$precargadas = [];
if($dimension_id > 0){
    $stmt = $conn->prepare("SELECT id, texto FROM preguntas_precargadas WHERE id_dimension=? ORDER BY id ASC");
    $stmt->bind_param("i", $dimension_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()){
        $precargadas[] = $row;
    }
    $stmt->close();
}

header('Content-Type: application/json');
echo json_encode($precargadas);
