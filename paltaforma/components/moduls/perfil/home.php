<?php

include '../../db/cn.php';
session_start();

$id = $_SESSION['key']['id'];
$usuario = $_SESSION['key']['usuario'];
$empresa = $_SESSION['key']['empresa'];

$query = "SELECT * FROM usuarios WHERE id = '$id'";
$result_task = mysqli_query($conn, $query);
$query2 = "SELECT * FROM log WHERE user = '$usuario' AND empresa = '$empresa' order by fecha desc LIMIT 50";
$result_task2 = mysqli_query($conn, $query2);
?>

<?php
foreach ($result_task as $row) {
?>
<script>
function changePassword() {
    Swal.fire({
        title: 'Nueva Contraseña',
        input: 'password',
        inputLabel: 'Ingresa tu nueva contraseña',
        inputPlaceholder: 'Nueva contraseña',
        inputAttributes: {
            maxlength: 20,
            autocapitalize: 'off',
            autocorrect: 'off'
        },
        showCancelButton: true,
        confirmButtonText: 'Cambiar',
        preConfirm: (newPassword) => {
            if (!newPassword) {
                Swal.showValidationMessage('Debes ingresar una contraseña');
            } else if (newPassword.length < 8) {
                Swal.showValidationMessage('La contraseña debe tener al menos 8 caracteres');
            } else {
                // Llamada AJAX para cambiar la contraseña en el servidor
                return fetch('./components/moduls/perfil/change_password.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            id: <?php echo $id; ?>,
                            password: newPassword
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(response.statusText);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Contraseña cambiada',
                                'La contraseña se ha actualizado exitosamente', 'success');
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error', 'No se pudo cambiar la contraseña', 'error');
                    });
            }
        }
    });
}


function updateProfile() {
    Swal.fire({
        title: 'Actualizar Perfil',
        html: `
            <div class="space-y-4 text-left">
                <div>
                    <label for="newName" class="block text-sm font-medium text-gray-700 text-left">Nombre</label>
                    <input type="text" id="newName" class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="<?php echo $row['nombre']; ?>">
                </div>
                <br>
                <div>
                    <label for="newUsuario" class="block text-sm font-medium text-gray-700 text-left">Usuario (Correo electrónico)</label>
                    <input type="text" id="newUsuario" class="w-full px-3 py-2 mt-1 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="<?php echo $row['usuario']; ?>">
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Actualizar',
        preConfirm: () => {
            const newName = document.getElementById('newName').value;
            const newUsuario = document.getElementById('newUsuario').value;

            // Validación de que los campos no estén vacíos
            if (!newName || !newUsuario) {
                Swal.showValidationMessage('Debes ingresar el nombre y el usuario');
                return false;
            }

            // Validación del formato de correo electrónico
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(newUsuario)) {
                Swal.showValidationMessage('Por favor ingresa un correo electrónico válido');
                return false;
            }

            // Llamada AJAX para actualizar el nombre y usuario en el servidor
            return fetch('./components/moduls/perfil/update_profile.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: <?php echo $id; ?>,
                        name: newName,
                        usuario: newUsuario
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire(
                            'Perfil actualizado',
                            'El nombre y usuario se han actualizado exitosamente. Tendrás que volver a ingresar para notar los cambios',
                            'success'
                        ).then(() => login()); // Recargar la página para ver cambios
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error', 'No se pudo actualizar el perfil', 'error');
                });
        }
    });
}
</script>

<div id="terceros">
    <div class="py-2 lg:px-4 mt-6 lg:mx-5">
        <div class="md:flex">
            <div class="md:basis-4/12 md:px-5">
                <div class="bg-white shadow-sm rounded h-full p-4 text-center">
                    <div class="pb-5 pt-10 mt-10">

                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQOsKzcgIHP83M8TgglSE_wVsb4XSfBtmv4MA&s"
                            class="w-12 h-12 text-5xl mx-auto text-center" alt="">
                    </div>
                    <p class="text-slate-700 font-semibold text-lg"><?php echo $row['nombre'] ?></p>
                    <p><?php echo $row['rol'] ?></p>

                    <p class="text-semibold text-purple-600 py-2"><?php echo $row['empresa'] ?></p>
                </div>
            </div>
            <div class="md:basis-8/12 md:px-5">
                <div class="bg-white shadow-sm rounded p-5">
                    <div class="p-4">
                        <p class="border-b border-slate-900 text-2xl text-slate-700 font-semibold p-2">Perfil</p>
                        <div class="mt-8">

                            <p class="text-lg text-slate-700 font-semibold">Detalles del Perfil</p>
                            <p class="mt-2 mb-4"><span class="text-slate-900 font-semibold">Nombre:</span>
                                <?php echo $row['nombre'] ?></p>
                            <p class="mt-2 mb-4"><span class="text-slate-900 font-semibold">Usuario:</span>
                                <?php echo $row['usuario'] ?></p>
                            <p class="mb-4"><span class="text-slate-900 font-semibold">Fecha de Registro:</span>
                                <?php echo $row['fecha_subida'] ?>
                            </p>
                            <p class="mb-4"><span class="text-slate-900 font-semibold">Tipo de usuario:</span>
                                <?php echo $row['rol'] ?>
                            </p>
                            <button onclick="changePassword()"
                                class="mt-4 px-4 py-2 bg-purple-600 text-white rounded">Cambiar Contraseña</button>
                            <button onclick="updateProfile()"
                                class="mt-4 px-4 py-2 bg-blue-600 text-white rounded">Actualizar Nombre y
                                Usuario</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="basis-12/12 px-5 py-5 mt-2">
            <div class="bg-white shadow-sm rounded p-4">
                <p class="border-b border-slate-900 text-2xl p-2 text-slate-700 font-semibold mb-2 md:mx-3">Logs</p>
                <div class="p-5">
                    <?php
                    foreach ($result_task2 as $row) {
                        echo '<p class="p-2 mb-3 border-b border-gray-200 hover:bg-gray-100"> <i class="bx bxs-calendar bg-purple-600 p-2 mr-5 text-white rounded"></i> Usuario '.$row['user'].' ingreso al modulo de '.substr(strchr($row['modul'], "moduls/"), 7).' el '.$row['fecha'].' en la empresa '.$row['empresa'].'<br><span>'.$row['context'].'<span></p>';

                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
}
?>