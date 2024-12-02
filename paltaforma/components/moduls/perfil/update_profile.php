<?php

include '../../db/cn.php';
session_start();

// Asegura que el cuerpo de la solicitud contiene JSON
$data = json_decode(file_get_contents('php://input'), true);

// Verifica que los datos necesarios estén presentes
if (!isset($data['id'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos.']);
    exit;
}

$v1 = mysqli_real_escape_string($conn, $data['id']);
$v2 = $data['name'];
$v3 = $data['usuario'];

// Consulta de actualización
$q = "UPDATE usuarios SET nombre='$v2', usuario='$v3' WHERE id='$v1'";
$result = mysqli_query($conn, $q);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Contraseña cambiada exitosamente.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al cambiar la contraseña.']);
}

?>
