<?php


include './components/header.php';

$usuario = $_SESSION['key']['usuario'] ? $_SESSION['key']['usuario'] : false;
$rol = $_SESSION['key']['rol'] ? $_SESSION['key']['rol'] : false;
$empresa = $_SESSION['key']['empresa'] ? $_SESSION['key']['empresa'] : false;

?>
<?php
if ($empresa != false && $empresa == 'ADMINISTRADOR') {
?>
<style>
.border-purple-600,
.border-purple-500 {
    border-color: #e2031a !important;
}

.text-purple-600,
.text-purple-500 {
    color: #e2031a !important;
}

.text-purple-600:hover,
.text-purple-500:hover {
    color: #e2031a !important;
}

.bg-purple-600,
.bg-purple-500 {
    background-color: #e2031a !important;
}

.bg-purple-600:hover,
.bg-purple-500:hover {
    background-color: #e2031a !important;
}
</style>
<?php  
}else{
?>
<style>
.border-purple-600,
.border-purple-500 {
    border-color: #e2031a !important;
}

.text-purple-600,
.text-purple-500 {
    color: #e2031a !important;
}

.text-purple-600:hover,
.text-purple-500:hover {
    color: #e2031a !important;
}

.bg-purple-600,
.bg-purple-500 {
    background-color: #e2031a !important;
}

.bg-purple-600:hover,
.bg-purple-500:hover {
    background-color: #e2031a !important;
}
</style>
<?php 
}
?>
<script>
var permisosData; // Variable global para almacenar los permisos

function permisos() {
    $.ajax({
        type: "POST",
        url: "./components/moduls/login/permisos.php",
        success: function(response) {
            if (response == 'error') {
                //login();
            } else {
                permisosData = JSON.parse(response);
            }

        }
    });
}

function logs(url) {
    var modul = url;
    $.ajax({
        type: "POST",
        data: {
            modul: modul
        },
        url: "./components/moduls/login/logs.php",
        success: function(response) {

        }
    });
}

var solicitudesAjax = []; // Array para almacenar todas las solicitudes AJAX activas

// Función para realizar solicitudes AJAX y almacenarlas en el array
function cargarContenido(url, successCallback) {
    permisos();
    $('#content').fadeOut(50, function() {
        cancelarTodasLasSolicitudes();

        var solicitud = $.ajax({
            type: "POST",
            url: url,
            timeout: 9000,
            data: {
                padre: 0,
            },
            success: function(response) {
                // Usar fadeIn para mostrar el nuevo contenido suavemente
                logs(url);
                $('#content').html(response).fadeIn(350, function() {
                    if (successCallback) {
                        successCallback();
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });

        // Almacena la solicitud en el array
        solicitudesAjax.push(solicitud);
    });
}

// Función para cancelar todas las solicitudes AJAX almacenadas
function cancelarTodasLasSolicitudes() {
    $.each(solicitudesAjax, function(index, solicitud) {
        solicitud.abort(); // Cancela la solicitud AJAX
    });
    solicitudesAjax = []; // Limpia el array de solicitudes
}

function documentos() {
    cargarContenido("./components/moduls/documentos/public/home.php");
}
// Funciones específicas para cargar contenido
function recibos_caja() {
    cargarContenido("./components/moduls/caja/home.php");
}

function list() {
    cargarContenido("./components/moduls/usuarios/list.php");
}

function home() {
    cargarContenido("./components/moduls/dashboard/home.php");
}

function historial() {
    cargarContenido("./components/moduls/dashboard/historial.php");
}

function perfil() {
    cargarContenido("./components/moduls/perfil/home.php");
}


function home() {
    $('#login').hide();
    $('#app,#app_nav , #logo-sidebar').show();
    $('#loading').hide();
    $('#cont').fadeIn(900);
    $('.listas').find('.border-b-2.border-purple-600').removeClass('border-b-2 border-purple-600');
    $('.listas #home').toggleClass('border-b-2 border-purple-600');
    cargarContenido("./components/moduls/dashboard/home.php");
}

function alerta_nopermisos() {
    Swal.fire({
        title: '¡Espera!',
        text: 'No tienes permiso para realizar esta accion.',
        icon: 'warning',
    })
};

function login() {
    $('#loading').show();
    $('#login').hide();
    $('#app,#app_nav , #logo-sidebar').hide();
    $.ajax({
        type: "POST",
        url: "./components/moduls/login/section.php",
        success: function(response) {
            $('#login').html(response);
            $('#login').fadeIn(1400, function() {
                $('#loading').fadeOut(1200);
            });
        }
    });
    $('.cont').toggleClass('hidden');
    $('body').toggleClass('bg-white');
}


$(document).ready(function() {
    <?php if (!isset($_SESSION['key']['empresa']) OR !isset($_SESSION['key']['usuario'])) : ?>
    login();
    <?php else: ?>
    home();
    <?php endif; ?>

    $('#historial').click(function() {
        historial();
        $('.listas').find('.border-b-2.border-purple-600').removeClass('border-b-2 border-purple-600');
        $(this).toggleClass('border-b-2 border-purple-600');
        return false;
    });
    $('#users').click(function() {
        <?php if (isset($_SESSION['key']['rol']) AND $_SESSION['key']['rol'] == 'Administrador') : ?>
        list();
        $('.listas').find('.border-b-2.border-purple-600').removeClass('border-b-2 border-purple-600');
        <?php else: ?>
        alerta_nopermisos();
        <?php endif; ?>
        return false;
    });

    $('#documentos').click(function() {
        documentos();
        $('.listas').find('.border-b-2.border-purple-600').removeClass('border-b-2 border-purple-600');
        $(this).toggleClass('border-b-2 border-purple-600');
        return false;
    });
    $('#rc').click(function() {
        recibos_caja();
        $('.listas').find('.border-b-2.border-purple-600').removeClass('border-b-2 border-purple-600');
        $(this).toggleClass('border-b-2 border-purple-600');
        return false;
    });

    $('#perfil').click(function() {
        perfil();
        $('.listas').find('.border-b-2.border-purple-600').removeClass('border-b-2 border-purple-600');
        return false;
    });

    $('#home').click(function() {
        home();
        $('.listas').find('.border-b-2.border-purple-600').removeClass('border-b-2 border-purple-600');
        $(this).toggleClass('border-b-2 border-purple-600');
        return false;
    });
    $('#home2').click(function() {
        home();
        return false;
    });

    $('#destroy').click(function() {
        // Mostrar confirmación con SweetAlert
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Si el usuario confirma, se ejecuta la solicitud AJAX
                $.ajax({
                    type: "POST",
                    url: "./destroy.php",
                    success: function(response) {
                        location.reload();
                        // Mostrar alerta de éxito
                        Swal.fire(
                            'Eliminado',
                            'El contenido ha sido eliminado.',
                            'success'
                        );
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        // Mostrar alerta de error
                        Swal.fire(
                            'Error',
                            'Hubo un problema al eliminar el contenido.',
                            'error'
                        );
                    }
                });
            }
        });

        return false; // Evita el comportamiento predeterminado del botón
    });

});
</script>

<div id="loading" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 9999; overflow: hidden;">
    <div class="flex items-center justify-center bg-black bg-opacity-85 h-full">
        <div class="loader ease-linear rounded-full border-t-4 border-purple-500 h-12 w-12 mb-4 animate-spin"></div>
    </div>
</div>


<div id="login" class="hidden" style="height: 100%; overflow:hidden"></div>

<nav class="fixed top-0 z-50 w-full bg-purple-600 border-gray-800 shadow-md hidden" id="app_nav">
    <div class="px-3 py-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center justify-start rtl:justify-end">
                <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar"
                    type="button"
                    class="inline-flex items-center p-2 text-sm text-white rounded-lg sm:hidden  focus:outline-none focus:ring-0 focus:ring-gray-200 ">
                    <span class="sr-only">Open sidebar</span>
                    <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 22 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path clip-rule="evenodd" fill-rule="evenodd"
                            d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z">
                        </path>
                    </svg>
                </button>
                <a href="" id="home2" class="flex ms-3 md:me-24 mt-1">
                    <span class="self-center text-lg font-semibold whitespace-nowrap text-white">
                        <span class="text-xl flex items-center">
                            <!-- Agregado 'flex items-center' para centrar verticalmente -->
                            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQOsKzcgIHP83M8TgglSE_wVsb4XSfBtmv4MA&s"
                                class="w-5 h-5 mr-2" alt="">

                            <!-- Agregado 'mr-2' para espacio a la derecha del icono -->
                            <span class="text-white font-semibold text-lg">UFPS</span>
                            <span class="text-black font-bold">Libs </span>

                        </span>
                        <span class="text-xs text-white">#SoyUFPS, Soy Calidad</span>
                    </span>
                </a>


            </div>
            <div class="flex items-center">
                <div class="flex items-center ms-5">
                    <div>
                        <button type="button" class="flex text-sm  text-white  px-2 mr-5 rounded-full"
                            aria-expanded="false" data-dropdown-toggle="dropdown-user">
                            <span class="sr-only">Open user menu</span>
                            <svg class="w-5 h-8" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M1 5h1.424a3.228 3.228 0 0 0 6.152 0H19a1 1 0 1 0 0-2H8.576a3.228 3.228 0 0 0-6.152 0H1a1 1 0 1 0 0 2Zm18 4h-1.424a3.228 3.228 0 0 0-6.152 0H1a1 1 0 1 0 0 2h10.424a3.228 3.228 0 0 0 6.152 0H19a1 1 0 0 0 0-2Zm0 6H8.576a3.228 3.228 0 0 0-6.152 0H1a1 1 0 0 0 0 2h1.424a3.228 3.228 0 0 0 6.152 0H19a1 1 0 0 0 0-2Z" />
                            </svg>
                        </button>
                    </div>
                    <div class="z-50 hidden my-8 text-base list-none bg-white divide-y divide-gray-100 rounded shadow "
                        id="dropdown-user">
                        <div class="px-4 py-4 bg-gray-100" role="none">
                            <span
                                class="self-center text-sm font-semibold  whitespace-nowrap "><?php echo strtoupper($_SESSION['key']['empresa']); ?></span>
                            <p class="text-sm text-gray-900 " role="none">
                                <?php echo $_SESSION['key']['nombre']; ?>
                            </p>
                            <p class="text-sm font-medium text-gray-900 truncate " role="none">
                                <?php echo $_SESSION['key']['usuario']; ?>
                            </p>
                        </div>
                        <ul class="py-1" role="none">
                            <li>
                                <a href="" id="perfil"
                                    class="block px-4 py-2 text-sm text-gray-600 hover:bg-gray-600 hover:text-white"
                                    role="menuitem">Perfil</a>
                            </li>
                            <li>
                                <a href="" id="destroy"
                                    class="block px-4 py-2 text-sm text-gray-600 hover:bg-gray-600 hover:text-white"
                                    role="menuitem">Salir</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<aside id="logo-sidebar"
    class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full mt-5 border-r border-gray-100 shadow-lg  sm:translate-x-0 bg-white hidden"
    aria-label="Sidebar">
    <div class="h-full px-5 pb-4 overflow-y-auto listas">
        <ul class="space-y-4 font-normal text-sm">
            <li>
                <a href="" id="home" class="flex items-center p-2 text-slate-900   hover:bg-gray-100  group">
                    <svg class="w-5 h-5 text-gray-900 transition duration-75 group-hover:text-slate-800 "
                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 24">
                        <path
                            d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z" />
                        <path
                            d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z" />
                    </svg>
                    <span class="ms-3">Home</span>
                </a>
            </li>
            <li>
                <a href="" id="historial" class="flex items-center p-2 text-slate-900   hover:bg-gray-100  group">
                    <svg class="w-5 h-5 text-gray-900 transition duration-75 group-hover:text-slate-800 "
                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 9h6m-6 3h6m-6 3h6M6.996 9h.01m-.01 3h.01m-.01 3h.01M4 5h16a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z" />
                    </svg>
                    <span class="ms-3">Historial</span>
                </a>
            </li>

            <li>
                <a href="" id="users"
                    class="flex items-center p-2 text-slate-900  fixed bottom-10 hover:text-red-600 group">
                    <svg class="w-5 h-5 text-gray-900 transition duration-75 group-hover:text-red-600"
                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 22 24">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 13v-2a1 1 0 0 0-1-1h-.8l-.7-1.7.6-.5a1 1 0 0 0 0-1.5L17.7 5a1 1 0 0 0-1.5 0l-.5.6-1.7-.7V4a1 1 0 0 0-1-1h-2a1 1 0 0 0-1 1v.8l-1.7.7-.5-.6a1 1 0 0 0-1.5 0L5 6.3a1 1 0 0 0 0 1.5l.6.5-.7 1.7H4a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h.8l.7 1.7-.6.5a1 1 0 0 0 0 1.5L6.3 19a1 1 0 0 0 1.5 0l.5-.6 1.7.7v.8a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1v-.8l1.7-.7.5.6a1 1 0 0 0 1.5 0l1.4-1.4a1 1 0 0 0 0-1.5l-.6-.5.7-1.7h.8a1 1 0 0 0 1-1Z" />
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                    </svg>
                    <span class="flex-1 ms-3 whitespace-nowrap">Configuracion</span>
                </a>
            </li>

        </ul>
    </div>
</aside>

<div class="p-4 sm:ml-64 cont" style="overflow:hidden">
    <div class="p-2 md:p-4 lg:px-12 lg:pt-4 mt-8">
        <div id="app" class="hidden">
            <section class="pt-3 pb-2 md:px-0 w-full" id="content"></section>
        </div>


    </div>
</div>



<?php
include './components/footer.php';

?>