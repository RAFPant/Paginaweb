<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['rol'] === "admin") {
    header("Location: index_admin.php");
} else {
    header("Location: index_encuestador.php");
}
exit;
