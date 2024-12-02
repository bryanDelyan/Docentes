<?php
include '../../db/cn.php';
$id = $_POST['id'];
$q = "SELECT * FROM contenido_01 where id = '$id'";
$result = mysqli_query($conn,$q);
foreach ($result as $row) {
?>
<script>

</script>
<form class="edit-view pt-4" data-padre="<?php echo $row['padre']; ?>" id="edit_form" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-7">
        <div class="">
            <div class="">
                <?php
                // Verifica si la columna 'imagen' contiene datos
                if (!empty($row['imagen'])) {
                      $imagen_src = './components/moduls/dashboard/docs/' . $row['imagen'];
                     //$imagen_src = 'https://lavozdelnorte.com.co/wp-content/uploads/2021/04/UFPS-C%C3%9ACUTA-on-Instagram_-_%E2%9D%A4-%C2%A1UFPS-Soy-yo_-Eres-t%C3%BA_-Somos-Todos_-__CBqIVqEnK_7JPG.jpg';
                } else {
                    // Si no hay imagen en la base de datos, muestra una imagen por defecto
                    $imagen_src = 'https://lavozdelnorte.com.co/wp-content/uploads/2021/04/UFPS-C%C3%9ACUTA-on-Instagram_-_%E2%9D%A4-%C2%A1UFPS-Soy-yo_-Eres-t%C3%BA_-Somos-Todos_-__CBqIVqEnK_7JPG.jpg';
                }
            ?>
                <div class="flex-shrink-0 h-[240px] w-full">
                    <img src="<?php echo $imagen_src; ?>" class="object-cover h-full w-full" id="preview-image-d" alt="">
                </div>
            </div>
            <div class="p-4">
                <input type="file" id="6_edit" name="6_edit" accept="image/*" class="hidden" />
                <label for="6_edit" class="cursor-pointer text-blue-500 underline">Subir imagen</label>
            </div>
        </div>
        <div class="bg-white p-4 col-span-3 h-full">
            <div class="grid gap-6 p-4 mb-6 md:grid-cols-1">
                <div>
                    <label for="first_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre
                        del
                        recurso</label>
                    <input type="text" id="1" name="1"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        value="<?php echo $row['titulo']; ?>" required />
                </div>
                <div>
                    <label for="text" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Descripcion
                        del
                        recurso</label>
                    <input type="text" id="2" name="2"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        value="<?php echo $row['descripcion']; ?>" required />
                </div>
                <div class="<?php if($row['padre'] != 0){ echo 'hidden';} ?>">
                    <label for="last_name"
                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Categoria o
                        Etiqueta</label>
                    <input type="text" id="3" name="3" <?php if($row['padre'] != 0){ echo ' value="1041050150150" ';} ?>
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        value="<?php echo $row['etiqueta']; ?>" required />
                </div>

            </div>

            <button type="submit" id="edit-content-form"
                class="edit-content-form text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Actualizar</button>
            <button type="submit"
                class="home text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Cancelar</button>
        </div>
</form>

<?php     
}
?>