<?php
include '../../db/cn.php';
session_start();

if ($_POST['action'] == 'add') {
    $usuario = isset($_SESSION['key']['usuario']) ? $_SESSION['key']['usuario'] : 0;
    $titulo = isset($_POST['1']) ? $_POST['1'] : 0;
    $descripcion = isset($_POST['2']) ? $_POST['2'] : 0;
    $etiqueta = isset($_POST['3']) ? $_POST['3'] : 0;
    $padre = isset($_POST['padre']) ? $_POST['padre'] : 0;
    $dw = isset($_POST['5']) ? $_POST['5'] : 0;
    

    // Handle the uploaded image
    if (isset($_FILES['6']) && $_FILES['6']['error'] == UPLOAD_ERR_OK) {
        $imageTmpName = $_FILES['6']['tmp_name'];
        $imageFileName = $_FILES['6']['name'];
        $imageFileType = strtolower(pathinfo($imageFileName, PATHINFO_EXTENSION));

        // Generar un nombre único para el archivo basado en la fecha y hora actual
        $date = new DateTime();
        $formattedDate = $date->format('Ymd_His'); // Formato: AñoMesDia_HoraMinutoSegundo
        $newFileName = $formattedDate . '.' . $imageFileType;
        
        $uploadDirectory = './docs/'; // Directorio donde se guardarán las imágenes
        $targetFilePath = $uploadDirectory . $newFileName;

        // Mover el archivo subido al directorio de destino con el nombre único
        if (move_uploaded_file($imageTmpName, $targetFilePath)) {
            // URL relativa de la imagen para almacenar en la base de datos
            $imagen_url = '' . $newFileName; // Ruta relativa desde la raíz del sitio

// Consulta para verificar si ya existe un registro con el mismo título
$check_query = "SELECT * FROM contenido_01 WHERE titulo='$titulo'";
$check_result = mysqli_query($conn, $check_query);

if (mysqli_num_rows($check_result) > 0) {
    // Si ya existe un registro con el mismo título
    echo 'Error: Ya existe un contenido con el mismo título.';
} else {
    // Insertar datos en la base de datos si no existe el título
    $q = "
    INSERT INTO contenido_01 
    SET usuario='$usuario', titulo='$titulo', descripcion='$descripcion', imagen='$imagen_url', etiqueta='$etiqueta', padre='$padre', dw='$dw'
    ";
    $result = mysqli_query($conn, $q);

    if ($result) {
        echo 'Contenido agregado exitosamente!';
    } else {
        echo 'Error al agregar contenido: ' . mysqli_error($conn);
    }
}

        } else {
            echo 'Error al mover la imagen al directorio de destino.';
        }
    } else {
        echo 'Error al subir la imagen.';
    }
}
if ($_POST['action'] == 'edit') {
    $usuario = isset($_SESSION['key']['usuario']) ? $_SESSION['key']['usuario'] : 0;
    $titulo = isset($_POST['1']) ? $_POST['1'] : 0;
    $descripcion = isset($_POST['2']) ? $_POST['2'] : 0;
    $etiqueta = isset($_POST['3']) ? $_POST['3'] : 0;
    $padre = isset($_POST['padre']) ? $_POST['padre'] : 0;
    $dw = isset($_POST['5']) ? $_POST['5'] : 0;
    $id = isset($_POST['id']) ? $_POST['id'] : 0;

    // Handle the uploaded image
    if (isset($_FILES['6_edit']) && $_FILES['6_edit']['error'] == UPLOAD_ERR_OK) {
        $imageTmpName = $_FILES['6_edit']['tmp_name'];
        $imageFileName = $_FILES['6_edit']['name'];
        $imageFileType = strtolower(pathinfo($imageFileName, PATHINFO_EXTENSION));

        // Generar un nombre único para el archivo basado en la fecha y hora actual
        $date = new DateTime();
        $formattedDate = $date->format('Ymd_His'); // Formato: AñoMesDia_HoraMinutoSegundo
        $newFileName = $formattedDate . '.' . $imageFileType;
        
        $uploadDirectory = './docs/'; // Directorio donde se guardarán las imágenes
        $targetFilePath = $uploadDirectory . $newFileName;

        // Mover el archivo subido al directorio de destino con el nombre único
        if (move_uploaded_file($imageTmpName, $targetFilePath)) {
            // URL relativa de la imagen para almacenar en la base de datos
            $imagen_url = '' . $newFileName; // Ruta relativa desde la raíz del sitio

            // Insertar datos en la base de datos
            $q = "
            UPDATE contenido_01 
            SET usuario='$usuario', titulo='$titulo', descripcion='$descripcion', imagen='$imagen_url', etiqueta='$etiqueta', padre='$padre', dw='$dw' WHERE id = '$id'";
            $result = mysqli_query($conn, $q);

            if ($result) {
                echo 'Contenido agregado exitosamente!';
            } else {
                echo 'Error al agregar contenido: ' . mysqli_error($conn);
            }
        } else {
            echo 'Error al mover la imagen al directorio de destino.';
        }
    } else {
        $q = "
            UPDATE contenido_01 
            SET usuario='$usuario', titulo='$titulo', descripcion='$descripcion', etiqueta='$etiqueta', dw='$dw' WHERE id = '$id'";
            $result = mysqli_query($conn, $q);

            if ($result) {
                echo 'Contenido editado exitosamente!';
            } else {
                echo 'Error al agregar contenido: ' . mysqli_error($conn);
            }
    }
}
if($_POST['action'] == 'remove'){
    $id = $_POST['id'];
    $q = "DELETE FROM contenido_01 WHERE id = '$id'";
    $result = mysqli_query($conn, $q);
    if ($result) {
        echo 'Contenido eliminado exitosamente!';
    } else {
        echo 'Error al agregar contenido: ' . mysqli_error($conn);
    }
};
if ($_POST['action'] == 'save_content') {
    $padre = $_POST['padre'];
    $content = $_POST['content'];

    // Consulta para verificar si ya existe el registro con el 'padre' dado
    $checkQuery = "SELECT COUNT(*) as total FROM detalle_01 WHERE padre = '$padre'";
    $checkResult = mysqli_query($conn, $checkQuery);
    $row = mysqli_fetch_assoc($checkResult);

    if ($row['total'] > 0) {
        // Si existe, actualiza el contenido
        $q = "UPDATE detalle_01 SET contenido = '$content' WHERE padre = '$padre'";
        $result = mysqli_query($conn, $q);
        if ($result) {
            echo 'Contenido editado exitosamente!';
        } else {
            echo 'Error al editar contenido: ' . mysqli_error($conn);
        }
    } else {
        // Si no existe, inserta un nuevo registro
        $insertQuery = "INSERT INTO detalle_01 (padre, contenido) VALUES ('$padre', '$content')";
        $insertResult = mysqli_query($conn, $insertQuery);
        if ($insertResult) {
            echo 'Contenido insertado exitosamente!';
        } else {
            echo 'Error al insertar contenido: ' . mysqli_error($conn);
        }
    }
}

?>