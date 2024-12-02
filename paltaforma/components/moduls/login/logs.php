<?php
include '../../db/cn.php';

session_start();
$empresa = $_SESSION['key']['empresa'];
$usuario = $_SESSION['key']['usuario'];
$modulo = $_POST['modul'];
$userAgent = $_SERVER['HTTP_USER_AGENT'];
// Configurar la zona horaria a Colombia
date_default_timezone_set('America/Bogota');

// Obtener la fecha y hora actual en formato Colombia
$fecha_hora_colombia = date('Y-m-d H:i:s');

// Preparar la consulta SQL
$q = "INSERT INTO log (fecha, modul, context, user, empresa) VALUES ('$fecha_hora_colombia', '$modulo', '$userAgent', '$usuario', '$empresa')";

// Ejecutar la consulta
$result = mysqli_query($conn, $q);

// Verificar el resultado de la consulta
if ($result) {
    echo "Registro insertado correctamente";
} else {
    echo "Error al insertar el registro: " . mysqli_error($conn);
}
?>
