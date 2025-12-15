<?php
$path = __DIR__ . '/config/conexion.php';
if (!file_exists($path)) {
  http_response_code(500);
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(['error' => 'Missing conexion.php']);
  exit;
}
require_once $path;

$sql = "SELECT p.id, p.id_dimension, p.texto, p.tipo, d.nombre AS dimension
  FROM preguntas p
  JOIN dimensiones d ON p.id_dimension = d.id
  WHERE p.activa = 1
  ORDER BY p.id_dimension, p.id";
$result = $conn->query($sql);
if ($result === false) {
  http_response_code(500);
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(['error' => 'Query failed: ' . $conn->error]);
  exit;
}


$preguntas = [];
while ($row = $result->fetch_assoc()) {
  // Asegurar que siempre exista el campo opciones (lista vacía por defecto)
  $row['opciones'] = [];

  if (isset($row['tipo']) && $row['tipo'] === 'cerrada') {
    // Obtener opciones para la pregunta cerrada
    $qid = (int)$row['id'];
    $optsSql = "SELECT id, texto_opcion, orden FROM opciones WHERE id_pregunta = $qid ORDER BY orden, id";
    $optsRes = $conn->query($optsSql);
    if ($optsRes !== false) {
      while ($opt = $optsRes->fetch_assoc()) {
        $row['opciones'][] = $opt;
      }
    }
    // Si no hay opciones en `opciones`, intentar `opciones_respuesta`
    if (empty($row['opciones'])) {
      $optsSql2 = "SELECT id, texto AS texto_opcion FROM opciones_respuesta WHERE id_pregunta = $qid ORDER BY id";
      $optsRes2 = $conn->query($optsSql2);
      if ($optsRes2 !== false) {
        while ($opt = $optsRes2->fetch_assoc()) {
          $row['opciones'][] = $opt;
        }
      }
    }
  }

  $preguntas[] = $row;
}

header('Content-Type: application/json; charset=UTF-8');
echo json_encode($preguntas);