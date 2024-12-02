<?php
include '../../db/cn.php';
session_start();
$empresa = $_SESSION['key']['empresa'];
if ($empresa == 'ADMINISTRADOR') {
    $query = "SELECT * FROM usuarios ORDER BY rol ASC";
    $query2 = "SELECT * FROM empresas order by nombre ASC";
}else{
    $query = "SELECT * FROM usuarios WHERE empresa = '$empresa' ORDER BY rol ASC";
    $query2 = "SELECT * FROM empresas WHERE nombre = '$empresa' order by nombre ASC";
}
$result_task2 = mysqli_query($conn, $query2);
$result_task = mysqli_query($conn, $query);

$query_roles = "SELECT * FROM roles WHERE empresa = '$empresa' order by nombre ASC";
$result_roles = mysqli_query($conn, $query_roles);

$query_permisos = "SELECT * FROM permisos order by modulo ASC";
$result_permisos = mysqli_query($conn, $query_permisos);

$query_permisos_autorizados = "SELECT rol,id_permiso,estado FROM permisos_rol  WHERE empresa = '$empresa'";
$result_permisos_autorizados = mysqli_query($conn, $query_permisos_autorizados);
$result_permisos_autorizados = mysqli_fetch_all($result_permisos_autorizados, MYSQLI_ASSOC);

$grupos_permisos = array();

foreach ($result_permisos_autorizados as $permiso) {
    $id_permiso = $permiso['id_permiso'];
    $estado = $permiso['estado'];

    if (!isset($grupos_permisos[$id_permiso])) {
        $grupos_permisos[$id_permiso] = array();
    }

    // Agregar el rol al grupo correspondiente
    $grupos_permisos[$id_permiso][$permiso['rol']] = $estado;
}


?>

<script>
$(document).ready(function() {
    $('.accordion-body').hide();

    $('.accordion-button').click(function() {
        var accordionBody = $(this).next('.accordion-body');
        $('.accordion-body').not(accordionBody).slideUp();
        accordionBody.slideToggle();
    });
});
$(document).off('click', '.on').on('click', '.on', function() {
    event.preventDefault();
    var rol = $(this).data('rol');
    var id_permiso = $(this).data('id');
    var self = this; // Almacena una referencia al elemento actual

    $.ajax({
        type: "POST",
        url: "./components/moduls/usuarios/funciones.php",
        data: {
            rol,
            id_permiso,
            action: 'removepermiso',
        },
        success: function(response) {
            // Usa la referencia almacenada para cambiar las clases
            console.log(response);
            $(self).removeClass("bx-toggle-left text-purple-500 on");
            $(self).addClass("bx-toggle-right text-gray-500 of");
        }
    });
});

$(document).off('click', '.of').on('click', '.of', function() {
    event.preventDefault();
    var rol = $(this).data('rol');
    var id_permiso = $(this).data('id');
    var self = this; // Almacena una referencia al elemento actual

    $.ajax({
        type: "POST",
        url: "./components/moduls/usuarios/funciones.php",
        data: {
            rol,
            id_permiso,
            action: 'addpermiso',
        },
        success: function(response) {
            // Usa la referencia almacenada para cambiar las clases
            console.log(response);
            $(self).removeClass("bx-toggle-right text-gray-500 of");
            $(self).addClass("bx-toggle-left text-purple-500 on");
        }
    });
});

$("#buscador").on("input", function() {
    var valorFiltro = $(this).val().toLowerCase();

    $("#tabla tbody tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(valorFiltro) > -1);
    });
});

$('#add_user').click(function() {
    $('#view-1').hide();
    $('#view-2').fadeIn('slow');
});

$('#add_rol').click(function() {
    // Muestra el SweetAlert2 con un input para el nombre del rol
    Swal.fire({
        title: 'Ingrese el nombre del rol',
        input: 'text',
        inputAttributes: {
            autocapitalize: 'off'
        },
        showCancelButton: true,
        confirmButtonText: 'Agregar',
        cancelButtonText: 'Cancelar',
        showLoaderOnConfirm: true,
        preConfirm: (nombreRol) => {
            // Realiza el AJAX para enviar el nombre del rol al servidor
            if (nombreRol !== '' && nombreRol !== null) {
                return $.ajax({
                    url: "./components/moduls/usuarios/funciones.php",
                    type: 'POST',
                    data: {
                        action: 'create_rol',
                        nombre_rol: nombreRol
                    },
                    success: function(response) {
                        // Maneja la respuesta del servidor aquí
                        response = JSON.parse(response);
                        console.log(response);
                        if (response.success) {
                            Swal.fire('Rol agregado correctamente', '', 'success');
                            list();
                        } else {
                            Swal.fire('Error al agregar el rol', response.message,
                                'error');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        // Maneja los errores de la solicitud AJAX aquí
                        Swal.fire('Error',
                            'Hubo un problema al realizar la solicitud AJAX',
                            'error');
                    }
                });
            } else {
                Swal.fire('Error', 'El nombre del rol no puede estar vacío o ser nulo', 'error');
            }
        },
        allowOutsideClick: () => !Swal.fire.isLoading()
    });
});

$('#form').submit(function() {
    // Obtiene el valor del campo de contraseña
    var password = $('#4').val(); // Asegúrate de que el selector coincida con el id del input

    // Verifica si la contraseña tiene al menos 8 caracteres
    if (password.length < 8) {
        Swal.fire({
            icon: "error",
            title: "Contraseña inválida",
            text: "La contraseña debe tener al menos 8 caracteres.",
        });
        return false; // Detiene el envío del formulario
    }

    var formData = $(this).serialize() + "&action=create";
    $.ajax({
        type: "POST",
        url: "./components/moduls/usuarios/funciones.php",
        data: formData,
        success: function(response) {
            console.log(response);
            if (response == 'Registro Creado') {
                Swal.fire({
                    icon: "success",
                    title: "Ok",
                    text: response,
                });
                $('#view-1').fadeIn('slow');
                list();
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: response,
                });
            }
        }
    });
    return false; // Evita el envío normal del formulario
});



$('.form-edit').submit(function() {
    var formData = $(this).serialize() + "&action=update";
    $.ajax({
        type: "POST",
        url: "./components/moduls/usuarios/funciones.php",
        data: formData,
        success: function(response) {
            if (response == 'Registro Actualizado') {
                Swal.fire({
                    icon: "success",
                    title: "Ok",
                    text: response,
                });
                list();
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: response,
                });
            }

        }
    });
    return false;
});
$('.eliminar-usuario').click(function() {
    var id = $(this).attr('data-id');
    console.log("w");

    $.ajax({
        type: "POST",
        url: "./components/moduls/usuarios/funciones.php",
        data: {
            id: id,
            action: 'delete'
        },
        success: function(response) {
            console.log(response);

            if (response == 'Registro eliminado') {
                Swal.fire({
                    icon: "success",
                    title: "Ok",
                    text: response,
                });
                list();
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: response,
                });
            }

        }
    });
});
$('.editar').click(function() {
    var id = $(this).attr('data-id');
    $('#form-' + id).fadeIn('slow');
    $('#view-1').hide();
    $('#view-2').hide();
});
$('.atras').click(function() {
    $('#view-2,.form-update').hide();
    $('#view-1').show();
});
tippy('#add_user', {
    content: 'Añadir usuarios',
});
tippy('#add_rol', {
    content: 'Crear Roles',
});
tippy('.editar', {
    content: 'Editar usuario',
});
tippy('.eliminar', {
    content: 'Eliminar usuario',
});

$('.eliminar_rol').click(function() {
    var id = $(this).attr('data-id');
    console.log("test");

    // Mostrar SweetAlert de confirmación
    Swal.fire({
            title: "¿Estás seguro?",
            text: "Una vez eliminado, no podrás recuperar este rol.",
            icon: "warning",
            showCancelButton: true, // Mostrar botón de cancelar
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar", // Texto personalizado para el botón de cancelar
            dangerMode: true,
        })
        .then((willDelete) => {
            // Si el usuario confirma (willDelete es true), realizar la solicitud AJAX
            if (willDelete) {
                // Realizar la solicitud AJAX
                $.ajax({
                    url: "./components/moduls/usuarios/funciones.php",
                    type: 'POST', // o 'DELETE' dependiendo de tu lógica de eliminación
                    data: {
                        id: id,
                        action: 'delete_rol',
                    },
                    success: function(response) {
                        console.log(response);

                        // Manejar la respuesta del servidor después de la eliminación
                        if (response == 'Rol eliminado correctamente') {
                            Swal.fire('success', 'Rol eliminado exitosamente', 'success');
                            list();
                        } else {

                            Swal.fire({
                                title: "Hubo un error",
                                icon: "error",
                                text: "Al parecer existen usuarios con este rol.",
                            });
                        }
                    },
                    error: function(error) {
                        console.log(response);
                        Swal.fire("Error al eliminar el rol", {
                            icon: "error",
                        });
                    }
                });
            } else {
                // El usuario canceló la eliminación
                Swal.fire("La eliminación ha sido cancelada.", {
                    icon: "info",
                });
            }
        });
});
</script>

<div id="usuarios">
    <div class="basis-12/12 py-5" id="view-1">
        <div class="w-fill mx-auto bg-white shadow-sm rounded p-2 rounded  mb-4">
            <div class="py-2 px-2">
                <div class="flex flex-row">
                    <div class="basis-10/12 mr-5">
                        <label for="default-search" class="mb-2 text-sm font-medium sr-only">Search</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                                </svg>
                            </div>
                            <input type="search" id="buscador"
                                class="block w-full p-3 ps-10 text-sm text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 border-0"
                                placeholder="Buscar por Rol, Nombre, etc" required>
                        </div>
                    </div>
                    <div class="basis-1/12 mt-1 text-center align-content-center">
                        <i id="add_user"
                            class='bx bxs-user-plus text-center bg-gray-900 text-white px-3 py-1 rounded text-lg'></i>
                    </div>
                    <div class="basis-1/12 mt-1 text-center align-content-center">
                        <i id="add_rol"
                            class='bx bx-buildings text-center bg-purple-500 text-white px-3 py-1 rounded text-lg'></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-fill mx-auto bg-white shadow-sm rounded mt-5">
            <div class="relative overflow-x-auto  px-2 py-5 ">
                <table class="w-full text-sm text-left rtl:text-right text-gray-900">
                    <tbody>
                        <?php foreach ($result_task2 as $row) { ?>
                        <tr class="bg-white">
                            <td class="pl-6 pr-0 py-2 font-medium text-gray-900 whitespace-nowrap">
                                <i class='bx bxs-data bg-purple-600 text-white rounded p-2 mr-4'></i>
                                <?php echo $row['nombre']; ?>
                            </td>

                            <td class="px-4 py-2">
                                Estado: <?php if ($row['estado'] == 1) {
                            echo 'Activo';
                          }else{echo 'Desactivado'; } ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5">
                                <!-- Asegura que esta celda abarque todas las columnas -->
                                <div class="w-fill mx-auto p-4 rounded">
                                    <div class="relative overflow-x-auto">
                                        <table class="w-full text-sm text-left rtl:text-right text-gray-900" id="tabla">
                                            <tbody>
                                                <?php foreach ($result_task as $row2) {
                                                if ($row['nombre'] == $row2['empresa']) { ?>
                                                <tr class="bg-white shadow-sm border-b ">
                                                    <td class="px-0 py-4">
                                                        <i class='bx bx-user text-black rounded p-2'></i>
                                                    </td>

                                                    <th scope="row"
                                                        class="px-4 py-4 font-medium text-gray-900 whitespace-nowrap">
                                                        <?php echo $row2['usuario']; ?>
                                                    </th>
                                                    <td class="px-4 py-4">
                                                        <?php echo $row2['nombre']; ?>
                                                    </td>
                                                    <td class="px-4 py-4">
                                                        <?php echo $row2['rol']; ?>
                                                    </td>
                                                    <td class="px-4 py-4">
                                                        <?php echo $row2['fecha_subida']; ?>
                                                    </td>
                                                    <td class="px-2 py-4 text-center">
                                                        <i data-id="<?php echo $row2['id']; ?>"
                                                            class='editar bx bx-edit-alt bg-slate-800 text-white rounded p-2'></i>
                                                    </td>
                                                    <td class="px-2 py-4 text-center">
                                                        <i data-id="<?php echo $row2['id']; ?>"
                                                            class='eliminar-usuario bx bx-trash-alt bg-purple-600 text-white rounded p-2'></i>
                                                    </td>
                                                </tr>
                                                <?php }
                                            } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>



    </div>

    <?php
    foreach ($result_task as $row) { // VISTA DE EDITAR USUARIO
    ?>
    <div class="basis-12/12 md:basis-6/12 py-4 px-4 hidden form-update mt-5" id="form-<?php echo $row['id']; ?>">
        <form class="max-w-md mx-auto bg-white shadow-sm p-6 rounded shadow-sm form-edit">
            <div class="mb-4 px-2 pt-6">
                <p class="font-semibold pb-2 atras hover:cursor-pointer"><i class='bx bx-chevron-left'></i> Atras</p>
            </div>
            <div class="grid gap-5 mb-4 md:grid-cols-1">
                <div class="mb-5 pt-3">
                    <label for="usuario" class="block mb-2 text-sm font-medium text-gray-900">Usuario</label>
                    <input type="email" id="1" name="1"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-slate-500 focus:border-slate-500 block w-full p-2.5"
                        value="<?php echo $row['usuario']; ?>" required>
                </div>
                <div class="mb-5">
                    <label for="contraseña" class="block mb-2 text-sm font-medium text-gray-900">Nombre</label>
                    <input type="text" id="2" name="2"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-slate-500 focus:border-slate-500 block w-full p-2.5"
                        value="<?php echo $row['nombre']; ?>" required>
                </div>
                <div class="mb-5">
                    <label for="usuario" class="block mb-2 text-sm font-medium text-gray-900">Rol</label>
                    <select name="3" id="3"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-slate-500 focus:border-slate-500 block w-full p-2.5">
                        <?php
                        foreach ($result_roles as $row2) {
                            if ($row['rol'] == $row2['nombre']) {
                                echo "<option value='".$row2['nombre']."' selected>".$row2['nombre']."</option>";
                            }else{
                            echo "<option value='".$row2['nombre']."'>".$row2['nombre']."</option>";
                            }
                        }
                    ?>
                    </select>
                </div>
                <div class="mb-5">
                    <label for="contraseña" class="block mb-2 text-sm font-medium text-gray-900">Contraseña</label>
                    <input type="text" id="4" name="4"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-slate-500 focus:border-slate-500 block w-full p-2.5"
                        value="<?php echo $row['contraseña']; ?>" required>
                </div>
            </div>
            <input type="hidden" name="id" id="id" value="<?php echo $row['id']; ?>">

            <button type="submit"
                class="text-white bg-gray-700 hover:bg-slate-800 focus:ring-4 focus:outline-none focus:ring-slate-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center">Actualizar
                Usuario</button>
        </form>
    </div>

    <?php
    }
    ?>
    <div class="basis-12/12 md:basis-4/12 py-6 hidden mt-5" id="view-2">

        <form class="max-w-md mx-auto bg-white shadow-sm p-6 rounded shadow-sm" id="form">
            <div class="mb-4 px-2 pt-6">
                <p class="font-semibold pb-2 atras hover:cursor-pointer"><i class='bx bx-chevron-left'></i> Atras</p>
            </div>
            <div class="mb-5 pt-3">
                <label for="usuario" class="block mb-2 text-sm font-medium text-gray-900">Empresa</label>
                <select value="<?php if(isset($empresa)){echo $empresa;} ?>"
                    <?php if($empresa != 'ADMINISTRADOR'){echo 'readonly ';} ?> required id="empresa" name="empresa"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-slate-500 focus:border-slate-500 block w-full p-2.5">
                    <?php
                    foreach ($result_task2 as $key) {
                        echo '<option value="'.$key['nombre'].'">'.$key['nombre'].'</option>';
                    }
                    ?>

                </select>
            </div>
            <div class="mb-5 pt-3">
                <label for="usuario" class="block mb-2 text-sm font-medium text-gray-900">Usuario</label>
                <input type="email" id="1" name="1"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-slate-500 focus:border-slate-500 block w-full p-2.5"
                    placeholder="example@gmail.com" required>
            </div>
            <div class="mb-5">
                <label for="contraseña" class="block mb-2 text-sm font-medium text-gray-900">Nombre</label>
                <input type="text" id="2" name="2"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-slate-500 focus:border-slate-500 block w-full p-2.5"
                    placeholder="Nombre" required>
            </div>
            <div class="mb-5">
                <label for="usuario" class="block mb-2 text-sm font-medium text-gray-900">Rol</label>
                <select name="3" id="3"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-slate-500 focus:border-slate-500 block w-full p-2.5">
                    <?php
                        foreach ($result_roles as $row) {
                            echo "<option value='".$row['nombre']."'>".$row['nombre']."</option>";
                        }
                    ?>
                </select>
            </div>
            <div class="mb-5">
                <label for="contraseña" class="block mb-2 text-sm font-medium text-gray-900">Contraseña</label>
                <input type="text" id="4" name="4"
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-slate-500 focus:border-slate-500 block w-full p-2.5"
                    placeholder="***" required>
            </div>
            <button type="submit"
                class="text-white bg-gray-700 hover:bg-slate-800 focus:ring-4 focus:outline-none focus:ring-slate-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center">Crear
                Usuario</button>
        </form>
    </div>
</div>