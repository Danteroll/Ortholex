<?php
// conexion.php
$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'ortholex';

$conexion = @new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conexion->connect_errno) {
  die('Error de conexión: ' . $conexion->connect_error);
}

// Charset correcto para acentos/ñ
if (!$conexion->set_charset('utf8mb4')) {
  die('Error al configurar charset: ' . $conexion->error);
}

// Helper: filtra POST sin “undefined index”
function field($name, $default = null)
{
  return isset($_POST[$name]) ? trim($_POST[$name]) : $default;
}
