<?php
session_start();
include("config/conexion.php");

if(isset($_GET['id'])){
    $id_productor = $_GET['id'];

    // Eliminar productor (encuestas y respuestas se borran automáticamente si ON DELETE CASCADE está activo)
    $stmt = $conn->prepare("DELETE FROM productores WHERE id = ?");
    $stmt->bind_param("i", $id_productor);

    if($stmt->execute()){
        $stmt->close();
        header("Location: buscar_productor.php?mensaje=Productor eliminado correctamente");
        exit;
    } else {
        echo "Error al eliminar el productor.";
    }
} else {
    echo "No se especificó el productor a eliminar.";
}
?>
