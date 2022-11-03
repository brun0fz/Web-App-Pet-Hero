<?php
include("header.php");
include("nav-bar.php");

?>

<div class="container px-4 py-5" id="featured-3">
    <h2 class="pb-2 border-bottom">Bienvenido/a <?php echo $_SESSION["loggedUser"]->getNombre() ?> :)</h2>
    <div class="row g-4 py-5 row-cols-1 row-cols-lg-3">
        <div class="feature col">
            <div class="feature-icon d-inline-flex align-items-center justify-content-center text-bg-primary bg-gradient fs-2 mb-3 p-2">
                <img class="img-unselect" src="<?php echo IMG_PATH . "reservasIcon.png" ?>" alt="" width="40" height="40">
            </div>
            <h3 class="fs-2">Reservas</h3>
            <p>Mira el listado de tus reservas solicitadas, confirmadas, en curso o ya finalizadas. Visualiza el detalle de una reserva. </p>
            <a href="<?php echo FRONT_ROOT . "Reserva/ShowListReservasView" ?>">Ver reservas</a><br>
        </div>
        <div class="feature col">
            <div class="feature-icon d-inline-flex align-items-center justify-content-center text-bg-primary bg-gradient fs-2 mb-3 p-2">
                <img class="img-unselect" src="<?php echo IMG_PATH . "disponibilidadIcon.png" ?>" alt="" width="40" height="40">
            </div>
            <h3 class="fs-2">Configuración</h3>
            <p>Selecciona los días en que estás disponible y el tamaño de las mascotas a cuidar. Establece el precio que cobras por día.</p>
            <a href="<?php echo FRONT_ROOT . "Guardian/ShowConfiguracionView" ?>">Establecer configuración</a>
        </div>
    </div>
    <?php if ((!$_SESSION["loggedUser"]->getDisponibilidad()) || (!$_SESSION["loggedUser"]->getTamanioMascotaCuidar()) || (!$_SESSION["loggedUser"]->getPrecioXDia())) { ?>
        <div class="alert alert-danger" style="width: fit-content;">
            <span class="mx-4"><b>(!)</b> Recuerda completar tus opciones de configuración para poder comenzar a recibir reservas.</span>
        </div>
    <?php } ?>
    <img class="background-img img-unselect" src="<?php echo IMG_PATH . "background.png" ?>" alt="">
</div>

<?php
include("footer.php");
?>