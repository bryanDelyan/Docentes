<?php
include '../../db/cn.php';
session_start();

if ($_POST['action'] === 'delete') {
    $id = $_POST['id'];
    $query = "DELETE FROM detalle_01 WHERE id = '$id'";
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Archivo eliminado correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar el archivo']);
    }
    exit();
}

if ($_POST['action'] == 'file_upload') {
    
// Recibir el valor de 'padre' del POST o de la sesión.
$padre = $_POST['padre'];

// Chequear si se ha recibido un archivo
if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
    // Información del archivo
    $file_name = $_FILES['file']['name'];
    $file_tmp = $_FILES['file']['tmp_name'];
    
    // Carpeta donde se almacenarán los archivos subidos
    $upload_dir = './uploaded_files/';
    
    // Verificar si la carpeta existe, de lo contrario crearla
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Generar una ruta única para evitar colisiones de nombres
    $target_file = $upload_dir . basename($file_name);
    
    // Mover el archivo a la carpeta de destino
    if (move_uploaded_file($file_tmp, $target_file)) {
        // Subida exitosa, ahora insertamos en la base de datos
        $contenido = ''; // Puedes modificar este valor si se envía también el contenido en otro campo del formulario

        $query = "INSERT INTO detalle_01 (padre, contenido, doc) VALUES ('$padre', '$contenido', '$file_name')";

        if (mysqli_query($conn, $query)) {
            echo json_encode(['success' => true, 'message' => 'Archivo subido e información guardada']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al guardar en la base de datos']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al mover el archivo']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No se ha recibido ningún archivo o hubo un error en la subida']);
}
}

?>