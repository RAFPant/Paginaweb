<?php
require_once "../config/conexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $texto = $_POST["texto"];
    $tipo = $_POST["tipo"];
    $id_dimension = $_POST["id_dimension"];

    // Insertar la pregunta
    $stmt = $conn->prepare("INSERT INTO preguntas (id_dimension, texto, tipo) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $id_dimension, $texto, $tipo);
    $stmt->execute();

    // Obtener ID de la nueva pregunta
    $id_pregunta = $conn->insert_id;

    // Si la pregunta es cerrada, guardar opciones múltiples
    if ($tipo == "cerrada" && !empty($_POST["opciones"])) {

        foreach ($_POST["opciones"] as $opc) {
            $opc = trim($opc);

            if ($opc !== "") {
                $opc_escapado = $conn->real_escape_string($opc);

                $conn->query("
                    INSERT INTO opciones_respuesta (id_pregunta, texto) 
                    VALUES ($id_pregunta, '$opc_escapado')
                ");
            }
        }
    }
}

header("Location: ../index.php");
exit;
?>
