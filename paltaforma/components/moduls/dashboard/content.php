<?php 
include '../../db/cn.php';
session_start();
$empresa = $_SESSION['key']['empresa'];

if ($_POST['action'] == 'view-content') {
    $padre = $_POST['padre'];
    $query = "SELECT * FROM contenido_01 WHERE padre = '$padre'";
    $result_task = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result_task) > 0) {
        ?>
<?php
        while ($contenido = mysqli_fetch_assoc($result_task)) {
            $imagen = $contenido['imagen'] ? './components/moduls/dashboard/docs/'.$contenido['imagen'] : 'https://lavozdelnorte.com.co/wp-content/uploads/2021/04/UFPS-C%C3%9ACUTA-on-Instagram_-_%E2%9D%A4-%C2%A1UFPS-Soy-yo_-Eres-t%C3%BA_-Somos-Todos_-__CBqIVqEnK_7JPG.jpg';
            ?>
<div class="relative group bg-white rounded shadow-lg overflow-hidden flex flex-col contenido"
    data-titulo="<?php echo htmlspecialchars($contenido['titulo']); ?>"
    data-descripcion="<?php echo htmlspecialchars($contenido['descripcion']); ?>">
    <div class="flex-shrink-0 h-[240px] w-full ">

        <img src="<?php echo $imagen; ?>" class="object-cover h-full w-full" alt="">
    </div>
    <div class="p-4 flex-grow flex flex-col">
        <h1 class="font-semibold"><?php echo htmlspecialchars($contenido['titulo']); ?></h1>
        <p class="flex-grow"><?php echo htmlspecialchars($contenido['descripcion']); ?></p>
        <div class="flex items-center justify-between mt-4">
            <button class="py-2 bg-purple-500 rounded text-white px-3 text-sm ver-data"
                data-contenido="<?php echo $contenido['id']; ?>">Ver contenido</button>
        </div>

        <?php 
                     if($_SESSION['key']['rol'] == 'Administrador' or $_SESSION['key']['rol'] == 'Profesor'){
                    ?>
        <div class="hidden absolute top-2 right-2 group-hover:flex space-x-2">
            <button data-id="<?php echo $contenido['id']; ?>"
                class="editar py-1 bg-slate-900 rounded-full text-white px-2">
                <i class='bx bx-edit'></i>
            </button>
            <button data-id="<?php echo $contenido['id']; ?>"
                onclick="borrar_contenido(<?php echo $contenido['id']; ?>, <?php echo $padre; ?>)"
                class="delete py-1 bg-slate-900 rounded-full text-white px-2">
                <i class='bx bx-trash-alt'></i>
            </button>
        </div>
        <?php 
                    }
                    ?>
    </div>
</div>
<?php
        }
        ?>
<?php
    } 

    if ($_SESSION['key']['rol'] == 'Administrador' || $_SESSION['key']['rol'] == 'Profesor') {
    ?>
<div class="relative group bg-white rounded shadow-lg overflow-hidden flex flex-col add-contenido" data-padre="0">
    <div class="flex items-center justify-center w-full h-full bg-gray-300 rounded">
        <svg class="w-10 h-10 text-gray-200 mx-auto" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
            fill="currentColor" viewBox="0 0 24 24">
            <path fill-rule="evenodd"
                d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10-10S2 17.523 2 12Zm11-4.243a1 1 0 1 0-2 0V11H7.757a1 1 0 1 0 0 2H11v3.243a1 1 0 1 0 2 0V13h3.243a1 1 0 1 0 0-2H13V7.757Z"
                clip-rule="evenodd" />
        </svg>
    </div>
</div>
<?php
    }
}
?>