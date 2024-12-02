<?php
    include '../../db/cn.php';

session_start();

$empresa = $_SESSION['key']['empresa'];
    
if ($_POST['action'] == 'addpermiso' ) {
    $id = $_POST['id_permiso'];
    $rol = $_POST['rol'];
    $query = "SELECT * FROM permisos_rol WHERE id_permiso = '$id' and rol = '$rol' and empresa = '$empresa' ORDER BY id desc"; 
    $result_task = mysqli_query($conn, $query);
    if ($result_task && mysqli_num_rows($result_task) > 0) {
        $query2 = "UPDATE permisos_rol SET estado = 1 WHERE id_permiso = '$id' and rol = '$rol' and empresa = '$empresa'";
    } else {
        $query2 = "INSERT INTO permisos_rol (id_permiso, rol, empresa, estado) VALUES ('$id', '$rol', '$empresa', 1)";
    }
    $result_task2 = mysqli_query($conn, $query2);
    if ($result_task2 === false) {
        die("Error en la consulta: " . mysqli_error($conn));
    } else {
        echo 'add';
    }
}
if ($_POST['action'] == 'removepermiso' ) {
    $id = $_POST['id_permiso'];
    $rol = $_POST['rol'];
    $query = "SELECT * FROM permisos_rol WHERE id_permiso = '$id' and rol = '$rol' and empresa = '$empresa' ORDER BY id desc"; 
    $result_task = mysqli_query($conn, $query);
    if ($result_task && mysqli_num_rows($result_task) > 0) {
        $query2 = "UPDATE permisos_rol SET estado = 2 WHERE id_permiso = '$id' and rol = '$rol' and empresa = '$empresa'";
    } else {
        $query2 = "INSERT INTO permisos_rol (id_permiso, rol, empresa, estado) VALUES ('$id', '$rol', '$empresa', 2)";
    }
    $result_task2 = mysqli_query($conn, $query2);
    if ($result_task2 === false) {
        die("Error en la consulta: " . mysqli_error($conn));
    } else {
        echo 'remove';
    }
}
if ($_POST['action'] == 'create_rol') {
    $rol = $_POST['nombre_rol'];
    $empresa = $_SESSION['key']['empresa'];

    // Verifica si ya existe un rol con el mismo nombre y empresa
    $existingQuery = "SELECT COUNT(*) as count FROM roles WHERE nombre = '$rol' AND empresa = '$empresa'";
    $existingResult = mysqli_query($conn, $existingQuery);
    $existingData = mysqli_fetch_assoc($existingResult);

    if ($existingData['count'] == 0) {
        // No existe un rol con el mismo nombre y empresa, procede a insertar
        $query = "INSERT INTO roles (nombre, empresa) VALUES ('$rol', '$empresa');";
        $result_task = mysqli_query($conn, $query);

        if ($result_task === false) {
            die("Error en la consulta: " . mysqli_error($conn));
        } else {
            // Devuelve una respuesta indicando que se creó correctamente
            echo json_encode(array('success' => true, 'message' => 'Rol agregado correctamente'));
        }
    } else {
        // Ya existe un rol con el mismo nombre y empresa, devuelve un mensaje indicando esto
        echo json_encode(array('success' => false, 'message' => 'El rol ya existe para esa empresa'));
    }
}
if ($_POST['action'] == 'delete_rol') {
    $id_rol = $_POST['id'];
    $empresa = $_SESSION['key']['empresa'];
    // Verifica si ya existe un rol con el mismo nombre y empresa
    $existingQuery = "SELECT COUNT(*) as count,nombre FROM roles WHERE nombre = '$id_rol' AND empresa = '$empresa'";
    $existingResult = mysqli_query($conn, $existingQuery);
    $existingData = mysqli_fetch_assoc($existingResult);

    if ($existingData['count'] > 0) {
        $rol = $existingData['nombre'];
        $checkAdminQuery = "SELECT COUNT(*) as count FROM usuarios WHERE rol = '$rol' AND empresa = '$empresa'";
        $checkAdminResult = mysqli_query($conn, $checkAdminQuery);
        $checkAdminData = mysqli_fetch_assoc($checkAdminResult);
        if ($checkAdminData['count'] > 0) {
            echo 'El rol aun tiene usuarios asignados';
        }else{
            // No existe un rol con el mismo nombre y empresa, procede a insertar
            $query = "DELETE FROM roles WHERE nombre = '$id_rol' AND empresa = '$empresa'";
            $result_task = mysqli_query($conn, $query);

            if ($result_task === false) {
                die("Error en la consulta: " . mysqli_error($conn));
            } else {
                // Devuelve una respuesta indicando que se creó correctamente
                echo 'Rol eliminado correctamente';
            }
        }
       
    } else {
        // Ya existe un rol con el mismo nombre y empresa, devuelve un mensaje indicando esto
        echo 'No se encontro rol por eliminar';
    }
}

if ($_POST['action'] == 'create') {
    $v1 = $_POST['1'];
    $v2 = $_POST['2'];
    $v3 = $_POST['3'];
    $v4 = $_POST['4'];
    $empresa = $_POST['empresa'];


    $validar = "SELECT usuario FROM usuarios WHERE usuario = '$v1'";
    $validator_q = mysqli_query($conn, $validar);
    
    // Verificar si la consulta de validación se ejecutó correctamente
    if ($validator_q === false) {
        die("Error en la consulta de validación: " . mysqli_error($conn));
    }
    
    // Comprobar si el usuario ya existe
    if (mysqli_num_rows($validator_q) > 0) {
        echo "Error: El usuario ya existe.";
    } else {
        // Insertar el nuevo usuario ya que no existe
        $query = "INSERT INTO usuarios (usuario, nombre, rol, contraseña, empresa) VALUES ('$v1', '$v2', '$v3', '$v4', '$empresa')";
        $result_task = mysqli_query($conn, $query);
    
        if ($result_task === false) {
            die("Error en la consulta de inserción: " . mysqli_error($conn));
        } else {
            echo "Registro Creado";
        }
    }
    
}
if ($_POST['action'] == 'update') {
    $id = $_POST['id'];
    $v1 = $_POST['1'];
    $v2 = $_POST['2'];
    $v3 = $_POST['3'];
    $v4 = $_POST['4'];
    $query = "UPDATE usuarios SET usuario = '$v1', nombre = '$v2', rol = '$v3', contraseña = '$v4' WHERE id = '$id'";
    $result_task = mysqli_query($conn, $query);

    if ($result_task === false) {
        die("Error en la consulta: " . mysqli_error($conn));
    } else {
        echo 'Registro Actualizado';
    }
}

if ($_POST['action'] == 'delete') {
    $id = $_POST['id'];
    $query = "DELETE FROM usuarios WHERE id = '$id'";
    $result_task = mysqli_query($conn, $query);

    if ($result_task === false) {
        die("Error en la consulta: " . mysqli_error($conn));
    } else {
        echo 'Registro eliminado';
    }
}

?>