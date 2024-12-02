<?php
include '../../db/cn.php';

session_start();
$empresa = $_SESSION['key']['empresa'];
$rol = $_SESSION['key']['rol'];
$id = $_SESSION['key']['id'];


if ($rol == 'Administrador') {
    $q2 = "SELECT * FROM usuarios where id = '$id'";
    $result2 = mysqli_query($conn, $q2);
    if (mysqli_num_rows($result2) > 0) {
        while ($row = mysqli_fetch_assoc($result2)) {
            if ($row['rol'] == 'Administrador') {
                for ($i = 1; $i <= 50; $i++) {
                    $permisoId = $i;
                    $permisosArray[$permisoId]['estado'] = 1;
                }
                echo json_encode($permisosArray);
            }else{
                echo 'error';
            }
        }
    }else{
        echo 'error';
    }
}else{
    $q = "SELECT * FROM permisos_rol where rol = '$rol' and empresa = '$empresa'";
    $result = mysqli_query($conn, $q);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Utiliza el id del permiso como índice en el array
            $permisoId = $row['id_permiso'];
            $permisosArray[$permisoId] = $row;
            echo json_encode($permisosArray);
        }
    }
    else {
            echo 'error';
    } 
} 



?>