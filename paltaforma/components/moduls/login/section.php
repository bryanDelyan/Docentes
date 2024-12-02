<?php
if (isset($_SESSION['key'])) {
  session_destroy();
  echo '<script>location.reload();</script>';
}


?>
<script>
$(document).ready(function() {
    $('#form_login').submit(function() {
        // Obtener los valores de los campos
        var user = $('#usuario').val().trim();
        var empresa = $('#empresa').val().trim();
        var contraseña = $('#contraseña').val().trim();

        // Limpiar los campos con valores sin espacios vacíos
        $('#usuario').val(user);
        $('#contraseña').val(contraseña);
        $.ajax({
            type: "POST",
            url: "./components/moduls/login/validation.php",
            data: {
                user,
                contraseña,
                empresa
            },
            success: function(response) {
                if (response ==
                    'Usuario autenticado correctamente') {
                        location.reload();
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
});
</script>

<div class="flex flex-row" style="height: 100%; overflow:hidden">
    <div class="basis-12/12 md:basis-6/12 shadow-lg flex flex-col justify-center px-20 py-15"
        style="background-color: #FDFEFE">
        <span class="self-left md:mx-20 md:px-20 text-lg font-semibold whitespace-nowrap text-white">
            <span class="text-2xl flex items-left">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQOsKzcgIHP83M8TgglSE_wVsb4XSfBtmv4MA&s"
                    class="w-8 h-8 mr-3 my-auto" alt="">
                <span class="text-black font-semibold">UFPS</span>
                <span class="text-purple-600 font-bold">Docentes

                </span>

            </span>
            <span class="text-black text-xs"></span>
        </span>

        <form class="md:px-20 py-8 md:mx-20" id="form_login">
            <div class="py-4 hidden">
                <label for="empresa" class="block mb-2 text-md font-medium text-slate-700">Empresa</label>
                <input type="text" autocomplete="off" id="empresa" name="empresa"
                    class="bg-gray-50 border border-gray-300 text-black text-md rounded focus:ring-slate-500 focus:border-slate-500 block w-full p-2.5"
                    placeholder="" value="UFPS" required>
            </div>
            <div class=" py-3">
                <label for="usuario" class="block mb-2 text-md font-medium text-slate-700">Usuario</label>
                <input type="text" autocomplete="off" id="usuario"
                    class="bg-gray-50 border border-gray-300 text-black text-md rounded focus:ring-slate-500 focus:border-slate-500 block w-full p-2.5"
                    placeholder=""" required>
        </div>
        <div class=" py-3">
                <label for="contraseña" class="block mb-2 text-md font-medium text-slate-700">Contraseña</label>
                <input type="password" autocomplete="off" id="contraseña"
                    class="bg-gray-50 border border-gray-300 text-black text-md rounded focus:ring-slate-500 focus:border-slate-500 block w-full p-2.5"
                    placeholder="*****" required>
            </div>
           
            <button type="submit"
                class="text-white bg-gray-700 hover:bg-slate-700 focus:ring-4 focus:outline-none focus:ring-slate-300 font-medium rounded text-sm w-full sm:w-auto px-5 py-2.5 mt-2 text-center">Login</button>
        </form>

    </div>
    <div class="basis-12/12 md:basis-6/12 bg-gray-50 relative hidden md:block">
        <div class="absolute inset-0 bg-black opacity-30"></div>
        <img src="./components/moduls/login/Fondo1.jpg" class="w-full h-full object-cover" alt="">
    </div>
</div>