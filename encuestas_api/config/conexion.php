<?php
$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = ''; // cambia si tienes contraseña
$DB_NAME = 'encuesta_db';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
  http_response_code(500);
  header('Content-Type: application/json; charset=UTF-8');
  echo json_encode(['error' => 'DB connection failed: ' . $conn->connect_error]);
  exit;
}
$conn->set_charset('utf8mb4');