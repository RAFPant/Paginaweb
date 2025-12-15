<?php
session_start();
include("config/conexion.php");

if(isset($_POST['usuario']) && isset($_POST['password'])){
    $usuario = $_POST['usuario'];
    $password = md5($_POST['password']); // tu tabla ya tiene MD5

    $sql = $conn->prepare("SELECT * FROM usuarios WHERE usuario=? AND password=?");
    $sql->bind_param("ss", $usuario, $password);
    $sql->execute();
    $resultado = $sql->get_result();

    if($resultado->num_rows == 1){
        $row = $resultado->fetch_assoc();
        $_SESSION['id'] = $row['id'];
        $_SESSION['usuario'] = $row['usuario'];
        $_SESSION['rol'] = $row['rol'];

        // Redirigir según rol
        if($row['rol'] == 'admin'){
            header("Location: index_admin.php");
            exit;
        } else {
            header("Location: index_encuestador.php");
            exit;
        }
    } else {
        $error = "Usuario o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Iniciar Sesión</h1>
    <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST">
        <label>Usuario:</label>
        <input type="text" name="usuario" required>
        <label>Contraseña:</label>
        <input type="password" name="password" required>
        <button type="submit">Entrar</button>
    </form>
</body>
</html>


