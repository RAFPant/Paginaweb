<?php
// 🔹 Eliminar cualquier espacio o texto antes de enviar el CSV
ob_clean();
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=respuestas_encuestas.csv');

// 🔹 Abrir salida del CSV
$output = fopen('php://output', 'w');

// 🔹 Encabezados del archivo CSV
fputcsv($output, ['Productor', 'Pregunta', 'Respuesta', 'Fecha']);

// 🔹 Conexión correcta
include('../config/conexion.php');

// 🔹 Consulta ajustada a tu estructura REAL
$sql = "
SELECT 
    pr.nombre_productor,
    p.texto AS pregunta,
    r.respuesta_texto AS respuesta,
    r.fecha_creacion AS fecha
FROM respuestas r
INNER JOIN encuestas_preguntas ep ON r.id_encuesta_pregunta = ep.id
INNER JOIN preguntas p ON ep.id_pregunta = p.id
INNER JOIN productores pr ON ep.id_encuesta = pr.id 
ORDER BY r.fecha_creacion DESC
";

$result = $conn->query($sql);

// 🔹 Escribir resultados
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['nombre_productor'],
            $row['pregunta'],
            $row['respuesta'],
            $row['fecha']
        ]);
    }
}

// 🔹 Cerrar CSV
fclose($output);
exit();
?>
