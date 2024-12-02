<?php

include '../../db/cn.php';

session_start();

// Leer la cookie de padreHistorial
if (isset($_COOKIE['padreHistorial'])) {
    $padreHistorial = json_decode($_COOKIE['padreHistorial'], true);
   
    
    // Si hay un historial de padres, hacer la consulta
    if (!empty($padreHistorial)) {
        
        // Mostrar una versión de la consulta para depuración
        $debug_query = "SELECT * FROM contenido_01 WHERE id IN (" . implode(',', $padreHistorial) . ")";
        $result_task = mysqli_query($conn,$debug_query);
    }
}
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
    // Obtener el historial del localStorage o inicializar como un array vacío si no existe
    var padreHistorial = JSON.parse(localStorage.getItem('padreHistorial')) || [];

    // Revisar si 'padre' ya existe en el historial
    const index = padreHistorial.indexOf(padre);
    if (index !== -1) {
        // Si existe, eliminarlo de su posición actual
        padreHistorial.splice(index, 1);
    }
    // Agregar 'padre' al inicio del historial
    padreHistorial.unshift(padre);

    // Guardar el historial actualizado en el localStorage
    localStorage.setItem('padreHistorial', JSON.stringify(padreHistorial));
    console.log(padreHistorial);
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

<div id="terceros" class="mt-14  md:px-4 xl:px-10 ">
    <h1 class="text-xl font-semibold">Historial de recursos</h1>
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
                    <p class="flex-grow text-sm text-justify"><?php echo htmlspecialchars($contenido['descripcion']); ?>
                    </p>

                    <div class="flex items-center justify-between mt-4">
                        <button class="py-2 bg-purple-500 rounded text-white px-3 text-sm ver-data"
                            data-contenido="<?php echo $contenido['id']; ?>">Ver contenido</button>

                    </div>
                </div>
            </div>
            <?php
        }
        ?>
        </div>
    </div>

    <div class="pag-2 hidden">
        <div>
            <div class="hidden pt-5" id="contenido_detalle"></div>
        </div>
        <div id="contenido_view" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4  gap-7 my-5"></div>
    </div>

</div>
</div>