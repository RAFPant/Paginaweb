<?php
include("../config/conexion.php");
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if($id <= 0) { http_response_code(400); echo json_encode(['error'=>'id']); exit; }

$stmt = $conn->prepare("SELECT id, texto, tipo FROM preguntas_precargadas WHERE id = ?");
$stmt->bind_param("i",$id);
$stmt->execute();
$res = $stmt->get_result();
if($row = $res->fetch_assoc()){
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['id'=>intval($row['id']),'texto'=>$row['texto'],'tipo'=>$row['tipo']]);
    exit;
}
http_response_code(404);
echo json_encode(['error'=>'not found']);
