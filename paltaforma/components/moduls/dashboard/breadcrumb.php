<?php
include '../../db/cn.php';

$padre = isset($_POST['padre']) ? $_POST['padre'] : 0;

// Función para obtener el camino completo del breadcrumb
function obtenerBreadcrumb($padre, $conn) {
    $breadcrumb = [];
    while ($padre != 0) {
        $query = "SELECT id, titulo, padre FROM contenido_01 WHERE id = $padre";
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);
        if ($row) {
            array_unshift($breadcrumb, $row);
            $padre = $row['padre'];
        } else {
            break;
        }
    }
    return $breadcrumb;
}

$breadcrumb = obtenerBreadcrumb($padre, $conn);

echo '<ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">';
echo '<li>';
echo '<div class="flex items-center">';
echo '<svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">';
echo '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />';
echo '</svg>';
echo '<a href="#" class="breadcrumb-link" data-padre="0">Home</a>';
echo '</div></li>';

foreach ($breadcrumb as $item) {
    echo '<li>';
    echo '<div class="flex items-center">';
    echo '<svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">';
    echo '<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4" />';
    echo '</svg>';
    echo '<a href="#" class="breadcrumb-link" data-padre="' . $item['id'] . '">' . $item['titulo'] . '</a>';
    echo '</div></li>';
}

echo '</ol>';
?>
