<?php

include '../../db/cn.php';

session_start();

$user = $_POST['user'];
$contraseña = $_POST['contraseña'];
$empresa = $_POST['empresa'];

$query = "SELECT u.id,u.empresa,u.usuario,u.nombre,u.rol,e.estado as estado,e.nit,e.telefono,e.correo,e.direccion 
FROM usuarios as u 
LEFT OUTER JOIN empresas as e on e.nombre = u.empresa  
WHERE u.empresa='$empresa' and u.usuario='$user' and u.contraseña='$contraseña'";
$result_task = mysqli_query($conn, $query);

if (mysqli_num_rows($result_task) > 0) {
    $res = mysqli_fetch_assoc($result_task);
    if ($res['estado'] == 1) {
        echo "Usuario autenticado correctamente";
        $_SESSION['key']['empresa'] = $res['empresa'];

        $_SESSION['key']['nit'] = $res['nit'];
        $_SESSION['key']['telefono'] = $res['telefono'];
        $_SESSION['key']['correo'] = $res['correo'];
        $_SESSION['key']['direccion'] = $res['direccion'];

        $_SESSION['key']['usuario'] = $res['usuario'];
        $_SESSION['key']['rol'] = $res['rol'];
        $_SESSION['key']['nombre'] = $res['nombre'];
        $_SESSION['key']['id'] = $res['id'];
    }else{
        echo 'Licencia caducada, contactarse con servicio tecnico';
    }
} else {
    echo "Usuario o contraseña incorrectos";
}

mysqli_free_result($result_task);
mysqli_close($conn);


?>