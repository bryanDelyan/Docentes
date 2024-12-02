<?php
if ($_FILES['upload']) {
    $uploadDirectory = 'uploads/'; // Cambia la ruta si es necesario
    $file = $_FILES['upload']['tmp_name'];
    $fileName = basename($_FILES['upload']['name']);
    $uploadFilePath = $uploadDirectory . $fileName;

    // Mueve el archivo al directorio de subida
    if (move_uploaded_file($file, $uploadFilePath)) {
        // Devuelve la URL de la imagen subida en formato JSON
        $response = [
            "url" => "/uploads/" . $fileName
        ];
        echo json_encode($response);
    } else {
        // En caso de error, envía una respuesta con el error
        http_response_code(500);
        echo json_encode(['error' => 'Error al subir la imagen']);
    }
}
?>
