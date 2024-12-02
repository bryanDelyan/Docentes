<?php 
include '../../db/cn.php';
session_start();
$empresa = $_SESSION['key']['empresa'];
$padre = $_POST['padre'];
$query = "SELECT * FROM detalle_01 where padre = '$padre'";
$result_task = mysqli_query($conn, $query);

$isAdminOrProfessor = $_SESSION['key']['rol'] == 'Administrador' || $_SESSION['key']['rol'] == 'Profesor';
?>
<style>
.ql-editor img {
    max-width: 100%;
    height: auto;
    cursor: nwse-resize;
}

.ql-toolbar.ql-snow,
.ql-container.ql-snow {
    border: 0px !important;
}

.ck.ck-editor__main,
.ck-toolbar_grouping {
    border: 0px !important;
}
</style>

<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-7">
    <div class="bg-white p-4 col-span-3 h-full" id="cont-<?php echo $padre; ?>">

        <?php if ($isAdminOrProfessor) { ?>
        <div class="text-right mb-2">
            <button id="save-editor-<?php echo $padre; ?>" class="bg-blue-500 text-white px-2 py-2 rounded"><i
                    class='bx bx-save'></i></button>
        </div>
        <?php } ?>
        <textarea id="editor-<?php echo $padre; ?>" class="quill-editor h-full border-0">
            <?php
            foreach ($result_task as $row){
                echo $row['contenido'];
                break;
            }
            ?>
        </textarea>
    </div>

    <div>
        <div class="flex items-start">
            <div>
                <?php foreach ($result_task as $row) { 
                if (empty($row['contenido']) OR $row['contenido'] == '') {
                ?>
                <div class="flex flex-col w-100 leading-1.5 p-2 border-gray-200 rounded">
                    <div class="flex items-start bg-white rounded w-fill p-2">
                        <div class="pt-2">
                            <span class="flex items-center gap-2 text-sm font-medium text-gray-900 w-full pb-2">
                                <svg class="w-5 h-5 text-gray-800" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M6 16v-3h.375a.626.626 0 0 1 .625.626v1.749a.626.626 0 0 1-.626.625H6Zm6-2.5a.5.5 0 1 1 1 0v2a.5.5 0 0 1-1 0v-2Z" />
                                    <path fill-rule="evenodd"
                                        d="M11 7V2h7a2 2 0 0 1 2 2v5h1a1 1 0 0 1 1 1v9a1 1 0 0 1-1 1h-1a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2H3a1 1 0 0 1-1-1v-9a1 1 0 0 1 1-1h6a2 2 0 0 0 2-2Zm7.683 6.006 1.335-.024-.037-2-1.327.024a2.647 2.647 0 0 0-2.636 2.647v1.706a2.647 2.647 0 0 0 2.647 2.647H20v-2h-1.335a.647.647 0 0 1-.647-.647v-1.706a.647.647 0 0 1 .647-.647h.018ZM5 11a1 1 0 0 0-1 1v5a1 1 0 0 0 1 1h1.376A2.626 2.626 0 0 0 9 15.375v-1.75A2.626 2.626 0 0 0 6.375 11H5Zm7.5 0a2.5 2.5 0 0 0-2.5 2.5v2a2.5 2.5 0 0 0 5 0v-2a2.5 2.5 0 0 0-2.5-2.5Z"
                                        clip-rule="evenodd" />
                                    <path d="M9 7V2.221a2 2 0 0 0-.5.365L4.586 6.5a2 2 0 0 0-.365.5H9Z" />
                                </svg>

                                <a Target="_blank"
                                    href="./components/moduls/dashboard/uploaded_files/<?php echo $row['doc']; ?>"
                                    dowload> <?php echo $row['doc']; ?></a>
                            </span>
                        </div>
                        <div class="inline-flex self-center items-center">
                            <?php if ($isAdminOrProfessor) { ?>
                            <button
                                class="eliminar-archivo inline-flex self-center items-center p-2 text-sm font-medium text-center text-gray-900 bg-gray-50 rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none"
                                data-id="<?php echo $row['id']; ?>">
                                Eliminar
                            </button>
                            <?php } ?>
                        </div>

                    </div>
                </div>
                <?php
                } 
            } ?>
                <?php if ($isAdminOrProfessor) { ?>
                <div class="flex flex-col w-100 leading-1.5 p-2 border-gray-200 rounded-e-xl rounded-es-xl">
                    <div class="flex items-start bg-white rounded-xl w-full pt-2 px-4">
                        <form id="upload-form" enctype="multipart/form-data">
                            <div class="inline-flex self-center items-center">
                                <!-- Botón personalizado para subir archivo -->
                                <label for="file-upload"
                                    class="inline-flex self-center items-center pt-2 text-sm font-medium text-center text-gray-900 hover:bg-gray-100 focus:ring-4 focus:outline-none cursor-pointer w-full">
                                    <i class='bx bx-upload'></i> Subir archivo
                                </label>

                                <input id="file-upload" type="file" name="file" class="hidden" />
                            </div>
                        </form>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {

    <?php if ($isAdminOrProfessor) { ?>
    // Si es administrador o profesor, inicializa CKEditor con todas las funciones
    initializeCKEditors(true);
    // Otras funciones que tienen permiso de edición
    <?php } else { ?>
    // Si no es administrador o profesor, inicializa CKEditor en modo read-only
    initializeCKEditors(false);
    <?php } ?>


    <?php if ($isAdminOrProfessor) { ?>
    $('#file-upload').on('change', function() {
        var formData = new FormData($('#upload-form')[0]);
        var padre = <?php echo $padre; ?>;
        formData.append('padre', <?php echo $padre; ?>); // Enviar el valor de 'padre'
        formData.append('action', 'file_upload');

        // Aquí haces la petición para subir el archivo usando jQuery AJAX
        $.ajax({
            url: './components/moduls/dashboard/upload.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                var res = JSON.parse(response);
                if (res.success) {
                    cargarData(padre);
                } else {
                    alert(res.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Error al subir el archivo');
                console.log('Error:', error);
            }
        });
    });

    $('#save-editor-<?php echo $padre; ?>').on('click', function() {
        const editorContent = $('#cont-<?php echo $padre; ?> .ck .ck-content ').html();
        var padre = <?php echo $padre ?>;
        $.ajax({
            url: './components/moduls/dashboard/api.php',
            type: 'POST',
            data: {
                action: 'save_content',
                padre: <?php echo $padre; ?>,
                content: editorContent
            },
            success: function(response) {
                if (response == 'Contenido editado exitosamente!') {
                    alert('Contenido guardado exitosamente.');
                    cargarData(padre);
                } else {
                    alert('Error al actualizar el contenido.');
                    console.log('Error:', response);
                }

            },
            error: function(xhr, status, error) {
                alert('Error al guardar el contenido.');
                console.log('Error:', response);
            }
        });
    });
    <?php } ?>

    $('.eliminar-archivo').on('click', function() {
        var id = $(this).data('id');
        var padre = <?php echo $padre; ?>;

        $.ajax({
            url: './components/moduls/dashboard/upload.php',
            type: 'POST',
            data: {
                action: 'delete',
                id: id,
                padre: padre
            },
            success: function(response) {
                var res = JSON.parse(response);
                if (res.success) {
                    cargarData(padre);
                } else {
                    alert(res.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Error al eliminar el archivo.');
                console.log('Error:', error);
            }
        });
    });
});

function initializeCKEditors(isEditable) {
    const editors = document.querySelectorAll('[id^="editor-"]');
    editors.forEach(editor => {
        ClassicEditor
            .create(editor, {
                toolbar: isEditable ? [
                    'heading', '|',
                    'bold', 'italic', 'underline', 'link', '|',
                    'bulletedList', 'numberedList', '|',
                    'insertTable', 'blockQuote', 'mediaEmbed', '|',
                    'undo', 'redo'
                ] : [],
                image: {
                    toolbar: isEditable ? [
                        'imageStyle:full',
                        'imageStyle:side',
                        '|',
                        'imageTextAlternative',
                        'resizeImage'
                    ] : []
                },
                simpleUpload: {
                    uploadUrl: './components/moduls/dashboard/img.php',
                    headers: {
                        'X-CSRF-TOKEN': 'CSRF-Token',
                        Authorization: 'Bearer <token>'
                    }
                },
                mediaEmbed: {
                    previewsInData: true
                },
                readOnly: !isEditable // Establecer modo de solo lectura
            })
            .catch(error => {
                console.error(error);
            });
    });
}
</script>