<?php
session_start();

if (!isset($_SESSION['id']) || $_SESSION['rol'] !== "admin") {
    header("Location: login.php");
    exit;
}
