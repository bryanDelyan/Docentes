<?php
include '../../db/cn.php';

session_start();

$query = "SELECT * FROM contenido_01 WHERE padre = 0";
$result_task = mysqli_query($conn, $query);

?>


<script>

// Actualizar el breadcrumb dinámicamente
function actualizarBreadcrumb(padre) {
    $.ajax({
        type: "POST",
        url: "./components/moduls/dashboard/breadcrumb.php",
        data: {
            padre: padre,
        },
        success: function(response) {
            $('#breadcrumb').html(response);
            actualizarPadre(padre);
        },
        error: function(xhr, status, error) {
            console.error(error);
            $('#breadcrumb').html('<p>Hubo un error al cargar el breadcrumb.</p>');
        }
    });
}

function actualizarPadre(padre) {

    $('#add_form').attr('data-padre', padre); // Usar .attr() para actualizar el atributo data-padre
    if (padre == 0) {
        // Muestra el div de categoría
        $('.div-cat').show();

        // Haz el input requerido
        $('#3').prop('required', true);
    } else {
        // Oculta el div de categoría
        $('.div-cat').hide();

        // No hace el input requerido
        $('#3').prop('required', false);
    }
}

function cargarData(padre) {
    var padreHistorial = getCookie('padreHistorial');
    if (padreHistorial) {
        padreHistorial = JSON.parse(padreHistorial);
    } else {
        padreHistorial = [];
    }

    // Si el padre no está en el historial, agregarlo
    if (!padreHistorial.includes(padre)) {
        padreHistorial.push(padre);
    }

    // Limitar el número de elementos en el historial (opcional, por ejemplo, máximo 10)
    if (padreHistorial.length > 10) {
        padreHistorial.shift(); // Eliminar el primer elemento (el más antiguo)
    }

    
    // Guardar el nuevo historial de padres en la cookie
    setCookie('padreHistorial', JSON.stringify(padreHistorial), 7); // 7 días de expiración
    console.log(padreHistorial);
    // Obtener la URL actual
    var currentUrl = new URL(window.location);

    // Revisar si el parámetro 'padre' ya existe, si es así, reemplazar su valor
    currentUrl.searchParams.set('id', padre);

    // Cambiar la URL sin recargar la página (sobrescribir el valor del parámetro 'padre')
    var newUrl = currentUrl.toString();
    history.pushState({
        padre: padre
    }, 'id', padre);


    $.ajax({
        type: "POST",
        url: "./components/moduls/dashboard/information.php",
        data: {
            action: 'view-content',
            padre: padre,
        },
        success: function(response) {
            $('.pag-1,#contenido_detalle, #contenido_view, .pag-2').hide();
            $('.pag-2').show();
            $('#contenido_detalle').hide().fadeIn(200).html(response);
            console.log(padre);
            if (window.DISQUS) {
                DISQUS.reset({
                    reload: true,
                    config: function() {
                        // Add padre to the page URL
                        this.page.url = window.location.href.split('?')[0] + "?padre=" + padre;
                        this.page.identifier = padre; // Use padre as the unique identifier
                        this.page.title = "Page for padre " +
                            padre; // Optional: Set a unique title
                    }
                });
            }
        },
        error: function(xhr, status, error) {
            console.error(error);
            error();
        }
    });
}
// Función para obtener el valor de una cookie por su nombre
function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i].trim();
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

// Función para establecer una cookie con un nombre, valor y días de expiración
function setCookie(name, value, days) {
    var d = new Date();
    d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = name + "=" + value + ";" + expires + ";path=/";
}
function editar_contenido(id) {
    $('#editar_contenido').empty();
    $.ajax({
        type: "POST",
        url: "./components/moduls/dashboard/editar.php",
        data: {
            id: id
        },
        success: function(response) {
            $('#editar_contenido').html(response);
            $('#editar_contenido').show();
        }
    });

}

function borrar_contenido(id, padre) {
    // Mostrar confirmación con SweetAlert
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción no se puede deshacer.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Si el usuario confirma, se ejecuta la solicitud AJAX
            $.ajax({
                type: "POST",
                url: "./components/moduls/dashboard/api.php",
                data: {
                    action: 'remove',
                    id: id,
                },
                success: function(response) {
                    $('.pag-1,#contenido_detalle').hide();
                    $('.pag-2, #contenido_view').show();
                    if (padre != 0) {
                        cargar_Contenido(padre);
                    } else {
                        home();
                    }
                    // Mostrar alerta de éxito
                    Swal.fire(
                        'Eliminado',
                        'El contenido ha sido eliminado.',
                        'success'
                    );
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    Swal.fire(
                        'Error',
                        'Hubo un problema al eliminar el contenido.',
                        'error'
                    );
                }
            });
        }
    });
}


function error() {
    $('#contenido_view').fadeIn(600).html('<p>Hubo un error al cargar el contenido.</p>');
}

function cargar_Contenido(padre) {

    
    $('#editar_contenido').empty();
    console.log("cargando padre: " + padre);
    // Obtener la URL actual
    var currentUrl = new URL(window.location);

    // Revisar si el parámetro 'padre' ya existe, si es así, reemplazar su valor
    currentUrl.searchParams.set('id', padre);

    // Cambiar la URL sin recargar la página (sobrescribir el valor del parámetro 'padre')
    var newUrl = currentUrl.toString();
    history.pushState({
        padre: padre
    }, 'id', padre);
    $.ajax({
        type: "POST",
        url: "./components/moduls/dashboard/content.php",
        data: {
            action: 'view-content',
            padre: padre,
        },
        success: function(response) {
            $('.pag-1,#contenido_detalle').hide();
            $('.pag-2, #contenido_view').show();
            $('#contenido_view').hide().fadeIn(200).html(response);
            actualizarPadre(padre);
            actualizarBreadcrumb(padre);
            if (window.DISQUS) {
                DISQUS.reset({
                    reload: true,
                    config: function() {
                        // Add padre to the page URL
                        this.page.url = window.location.href.split('?')[0] + "?padre=" + padre;
                        this.page.identifier = padre; // Use padre as the unique identifier
                        this.page.title = "Page for padre " +
                            padre; // Optional: Set a unique title
                    }
                });
            }
        },
        error: function(xhr, status, error) {
            console.error(error);
            alert('Error al cargar el contenido.'); // Puedes mostrar un mensaje de error más informativo
        }
    });
}

// Al cargar el documento, configuramos los eventos
$(document).ready(function() {


    $('#filtro-select').on('change', function() {
        var query = $(this).val().toLowerCase();

        if (query != 0) {
            $('.contenido').each(function() {
                var categoria = $(this).data('categoria').toLowerCase();

                if (categoria == query) {
                    $(this).fadeIn('600');
                } else {
                    $(this).hide();
                }
            });
        } else {
            $('.contenido').each(function() {
                $(this).fadeIn('600');
            });
        }
    });

    $('#buscador').on('input', function() {
        var query = $(this).val().toLowerCase();

        $('.contenido').each(function() {
            var titulo = $(this).data('titulo').toLowerCase();
            var descripcion = $(this).data('descripcion').toLowerCase();

            if (titulo.includes(query) || descripcion.includes(query)) {
                $(this).fadeIn('600');
            } else {
                $(this).hide();
            }
        });
    });

    $('.home').click(function() {
        var currentUrl = new URL(window.location);

// Revisar si el parámetro 'padre' ya existe, si es así, reemplazar su valor
currentUrl.searchParams.set('id', 0);

// Cambiar la URL sin recargar la página (sobrescribir el valor del parámetro 'padre')
var newUrl = currentUrl.toString();
history.pushState({
    padre: 0
}, 'id', 0);
        $('.add-view,.pag-2').hide();
        $('.pag-1').show();
        $('#add_form').attr('data-padre', 0);
    });

    // Escuchar el clic en los enlaces del breadcrumb
    $(document).on('click', '.breadcrumb-link', function(e) {
        $('#editar_contenido').empty();
        e.preventDefault();
        var padre = $(this).data('padre');
        if (padre === 0) {
            $('.pag-1').fadeIn('600');
            $('.pag-2,.add-view').hide();
            $('#breadcrumb').fadeIn(600).html(`
                <div class="inline-flex items-center">
                    <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />
                    </svg>
                    <a href="#" class="breadcrumb-link" data-padre="0">Home</a>
                </div>
            `);
        }
    });

    // Remover eventos anteriores y luego agregar
    $(document).off('click', '.ver-contenido').on('click', '.ver-contenido', function() {
        var padre = $(this).data('contenido');
        cargar_Contenido(padre);
    });

    // Remover eventos anteriores y luego agregar
    $(document).off('click', '.ver-data').on('click', '.ver-data', function() {
        var padre = $(this).data("contenido");
        cargarData(padre);
    });

    // Remover eventos anteriores y luego agregar
    $(document).off('click', '.editar').on('click', '.editar', function() {
        var id = $(this).data("id");
        $('.pag-1, .pag-2, .add-view').hide();
        editar_contenido(id);
    });

    // Remover eventos anteriores y luego agregar
    $(document).off('click', '.add-contenido').on('click', '.add-contenido', function() {
        $('.add-view').show();
        $('.pag-1, .pag-2').hide();
    });

    // Remover eventos anteriores y luego agregar
    $(document).off('change', '#6').on('change', '#6', function() {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#preview-image').attr('src', e.target.result);
        }
        reader.readAsDataURL(this.files[0]);
    });
    $(document).off('change', '#6_edit').on('change', '#6_edit', function() {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#preview-image').attr('src', e.target.result);
        }
        reader.readAsDataURL(this.files[0]);
    });
    // Remover eventos anteriores y luego agregar
    $(document).off('submit', '#add_form').on('submit', '#add_form', function(e) {
        e.preventDefault(); // Prevent the default form submission
        var padre = $(this).data('padre');
        var formData = new FormData(this);
        formData.append('action', 'add');
        formData.append('padre', padre); // Get the value of padre

        $.ajax({
            type: "POST",
            url: "./components/moduls/dashboard/api.php",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('Contenido agregado:', response);
                if (response != 'Error: Ya existe un contenido con el mismo título.') {
                    if (padre != 0) {
                        cargar_Contenido(padre);
                    } else {
                        home();
                    }
                    $('#add_form').hide().find('input, textarea, select').val('').prop(
                        'checked', false);
                } else {
                    Swal.fire(
                        'Error',
                        'Ya existe un contenido con el mismo titulo.',
                        'error'
                    );
                }

            },
            error: function(xhr, status, error) {
                console.error('Error al agregar contenido:', error);
                alert('Hubo un error al agregar el contenido. Inténtalo de nuevo.');
                if (padre != 0) {
                    cargar_Contenido(padre);
                } else {
                    home();
                }
                $('#add_form').hide().find('input, textarea, select').val('').prop(
                    'checked', false);
            }
        });
        $('#editar_contenido').empty();
    });
    $(document).off('submit', '#edit_form').on('submit', '#edit_form', function(e) {
        e.preventDefault(); // Prevent the default form submission
        var padre = $(this).data('padre');
        var formData = new FormData(this);
        formData.append('action', 'edit');
        formData.append('padre', padre); // Get the value of padre

        $.ajax({
            type: "POST",
            url: "./components/moduls/dashboard/api.php",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log('Contenido agregado:', response);
                if (padre != 0) {
                    cargar_Contenido(padre);
                } else {
                    home();
                }
                $('#editar_contenido').html('');
            },
            error: function(xhr, status, error) {
                console.error('Error al agregar contenido:', error);
                alert('Hubo un error al agregar el contenido. Inténtalo de nuevo.');
                if (padre != 0) {
                    cargar_Contenido(padre);
                } else {
                    home();
                }
                $('#add_form').hide().find('input, textarea, select').val('').prop(
                    'checked', false);
            }
        });
    });
    $('#editar_contenido').empty();
    actualizarBreadcrumb(0);
});
</script>

</script>

<div id="terceros" class="mt-7  md:px-4 xl:px-10 ">

    <div class="w-fill mx-auto bg-white shadow-sm rounded p-2 rounded mt-6">
        <div class="py-2 px-2">
            <label for="default-search" class="mb-2 text-sm font-medium sr-only">Search</label>

            <div class="grid grid-cols-12 gap-4">
                <!-- Div del buscador que ocupa 10 columnas -->
                <div class="relative col-span-9">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                        </svg>
                    </div>
                    <input type="search" id="buscador"
                        class="block w-full p-3 ps-10 bg-gray-100 text-sm text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 border-0"
                        placeholder="Buscar por titulo, contenido" required>
                </div>

                <!-- Div del filtro que ocupa 2 columnas -->
                <div class="relative col-span-1">
                    <select id="filtro-select"
                        class="p-3 text-sm bg-white border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="0" selected>Seleccionar filtro</option>
                        <?php 
                $tipos_mostrados = array();
                foreach ($result_task as $contenido) {
                    if (!in_array($contenido['etiqueta'], $tipos_mostrados)) {
                        echo '<option value="'.$contenido['etiqueta'].'">' . $contenido['etiqueta'] . '</option>';
                        $tipos_mostrados[] = $contenido['etiqueta'];
                    }
                }
            ?>
                    </select>
                </div>
            </div>


            <div class="pt-2 ">
                <nav class="flex" aria-label="Breadcrumb" id="breadcrumb">
                    <!-- Breadcrumb dinámico cargado aquí -->
                </nav>
            </div>
        </div>

    </div>

    <div class="pag-1">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4 gap-7 my-5">
            <?php
        foreach ($result_task as $contenido) {
        ?>
            <div class="relative group bg-white rounded shadow-lg overflow-hidden flex flex-col contenido"
                data-titulo="<?php echo htmlspecialchars($contenido['titulo']); ?>"
                data-descripcion="<?php echo htmlspecialchars($contenido['descripcion']); ?>"
                data-categoria="<?php echo htmlspecialchars($contenido['etiqueta']); ?>">
                <?php
                // Verifica si la columna 'imagen' contiene datos
                if (!empty($contenido['imagen'])) {
                      $imagen_src = './components/moduls/dashboard/docs/' . $contenido['imagen'];
                     //$imagen_src = 'https://lavozdelnorte.com.co/wp-content/uploads/2021/04/UFPS-C%C3%9ACUTA-on-Instagram_-_%E2%9D%A4-%C2%A1UFPS-Soy-yo_-Eres-t%C3%BA_-Somos-Todos_-__CBqIVqEnK_7JPG.jpg';
                } else {
                    // Si no hay imagen en la base de datos, muestra una imagen por defecto
                    $imagen_src = 'https://lavozdelnorte.com.co/wp-content/uploads/2021/04/UFPS-C%C3%9ACUTA-on-Instagram_-_%E2%9D%A4-%C2%A1UFPS-Soy-yo_-Eres-t%C3%BA_-Somos-Todos_-__CBqIVqEnK_7JPG.jpg';
                }
            ?>
                <div class="flex-shrink-0 h-[240px] w-full">
                    <img src="<?php echo $imagen_src; ?>" class="object-cover h-full w-full" alt="">
                </div>

                <div class="p-4 flex-grow flex flex-col">
                    <h1 class="font-semibold"><?php echo htmlspecialchars($contenido['titulo']); ?></h1>
                    <p class="text-xs"><i class='bx bx-tag'></i> <?php echo htmlspecialchars($contenido['etiqueta']); ?>
                    </p>
                    <p class="flex-grow text-sm text-justify"><?php echo htmlspecialchars($contenido['descripcion']); ?>
                    </p>

                    <div class="flex items-center justify-between mt-4">
                        <button class="py-2 bg-purple-500 rounded text-white px-3 text-sm ver-contenido"
                            data-contenido="<?php echo $contenido['id']; ?>">Ver contenido</button>

                    </div>

                    <?php 
                if($_SESSION['key']['rol'] == 'Administrador' or $_SESSION['key']['rol'] == 'Profesor'){
                ?>
                    <div class="hidden absolute top-2 right-2 group-hover:flex space-x-2">
                        <button data-id="<?php echo $contenido['id']; ?>"
                            class="editar py-1 bg-slate-900 rounded-full text-white px-2"><i
                                class='bx bx-edit'></i></button>
                        <button data-id="<?php echo $contenido['id']; ?>"
                            onclick="borrar_contenido(<?php echo $contenido['id'] ?>, 0)"
                            class="delete py-1 bg-slate-900 rounded-full text-white px-2"><i
                                class='bx bx-trash-alt'></i></button>
                    </div>
                    <?php 
                }
                ?>
                </div>
            </div>
            <?php
        }

        if($_SESSION['key']['rol'] == 'Administrador' or $_SESSION['key']['rol'] == 'Profesor'){
        ?>
            <div class="relative group bg-white rounded shadow-lg overflow-hidden flex flex-col add-contenido"
                data-padre="0">
                <div class="flex items-center justify-center w-full h-full bg-gray-300 rounded">
                    <svg class="w-10 h-10 text-gray-200 mx-auto" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd"
                            d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4.243a1 1 0 1 0-2 0V11H7.757a1 1 0 1 0 0 2H11v3.243a1 1 0 1 0 2 0V13h3.243a1 1 0 1 0 0-2H13V7.757Z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
            <?php
         }
        ?>
        </div>
    </div>
    <div id="editar_contenido" class="hidden">
    </div>
    <div class="pag-2 hidden">
        <div>
            <div class="hidden pt-5" id="contenido_detalle"></div>
        </div>
        <div id="contenido_view" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4  gap-7 my-5"></div>
    </div>

    <form class="add-view hidden pt-4" data-padre="0" id="add_form" enctype="multipart/form-data">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-7">
            <div class="">
                <div class="">
                    <img id="preview-image"
                        src="https://lavozdelnorte.com.co/wp-content/uploads/2021/04/UFPS-C%C3%9ACUTA-on-Instagram_-_%E2%9D%A4-%C2%A1UFPS-Soy-yo_-Eres-t%C3%BA_-Somos-Todos_-__CBqIVqEnK_7JPG.jpg"
                        class="w-100 h-100" alt="Imagen de vista previa">
                </div>
                <div class="p-4">
                    <input type="file" id="6" name="6" accept="image/*" class="hidden" required />
                    <label for="6" class="cursor-pointer text-blue-500 underline">Subir imagen</label>
                </div>
            </div>
            <div class="bg-white p-4 col-span-3 h-full">
                <div class="grid gap-6 p-4 mb-6 md:grid-cols-1">
                    <div>
                        <label for="first_name"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre del
                            recurso</label>
                        <input type="text" id="1" name="1"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="" required />
                    </div>
                    <div>
                        <label for="text"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Descripcion del
                            recurso</label>
                        <input type="text" id="2" name="2"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="" required />
                    </div>
                    <div class="div-cat">
                        <label for="last_name"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Categoria o
                            Etiqueta</label>
                        <input type="text" id="3" name="3"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            required />
                    </div>

                </div>

                <div class="flex items-start mb-6 hidden">
                    <div class="flex items-center h-5">
                        <input type="checkbox" value="1" id="5" name="5"
                            class="w-4 h-4 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-blue-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-600 dark:ring-offset-gray-800" />
                    </div>
                    <label for="remember" class="ms-2 text-sm font-normal text-gray-900">Se puede descargar</label>
                </div>
                <button type="submit" id="add-content-form"
                    class="add-content-form text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Guardar</button>
                <button type="submit"
                    class="home text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Cancelar</button>
            </div>
    </form>


</div>
</div>