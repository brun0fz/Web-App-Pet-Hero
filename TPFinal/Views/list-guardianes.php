<?php
include("header.php");
include("navBar.php");
?>


<div>
    <form action="<?php echo FRONT_ROOT ?>Duenio/FiltrarGuardianes" method="Post">
        <input type="date" name="fechaInicio" id="">
        <input type="date" name="fechaFin" id="">
        <input type="submit" value="Enviar">
    </form>
</div>

<div class="container">
    <div class="list-guardianes">
        <h2 id="list-title">Guardianes</h2><br>
        <?php foreach ($listaGuardianes as $guardian) {
            if ($guardian->getDisponibilidad() && $guardian->getPrecioXDia() != 0) { ?>
                <!--Solo muestra guardianes con Disponibilidad y PrecioxDia seteados-->
                <div class="card mb-3 shadow-sm">
                    <div class="row g-0">
                        <div class="col-md-4 card-img-guardian">
                            <img src="<?php echo IMG_PATH . $guardian->getRutaFoto() ?>" class="img-fluid rounded-start img-guardian">
                        </div>
                        <div class="col-md-8 p-1">
                            <div class="card-body">
                                <h3 class="card-title"><b><?php echo $guardian->getNombre() . " " . $guardian->getApellido(); ?></b></h3>
                                <?php
                                $n = 0;
                                for ($i = 1; $i <= (int)$guardian->getReputacion(); $i++) {
                                ?><img src="<?php echo IMG_PATH . "pawFull.png"; ?>" class="p-1" width="30" height="30" alt=""><?php
                                                                                                                                $n = $i;
                                                                                                                            }
                                                                                                                            if (($guardian->getReputacion() - (int)$guardian->getReputacion()) >= 0.5) {
                                                                                                                                ?><img src="<?php echo IMG_PATH . "pawHalf.png"; ?>" class="p-1" width="30" height="30" alt=""><?php
                                                                                                                                                                                                                                $n++;
                                                                                                                                                                                                                            }
                                                                                                                                                                                                                            if ($i <= 5) {
                                                                                                                                                                                                                                for ($i = $n + 1; $i <= 5; $i++) {
                                                                                                                                                                                                                                ?><img src="<?php echo IMG_PATH . "pawEmpty.png"; ?>" class="p-1" width="30" height="30" alt=""><?php
                                                                                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                                                                                                                ?>
                                <br><br>
                                <p class="card-text">Precio por día: <b><?php echo "$" . $guardian->getPrecioXDia(); ?></b></p>
                                <p class="card-text">Dirección: <b><?php echo $guardian->getCalle() . " " . $guardian->getNumero(); ?></b></p>
                                <p class="card-text">Disponibilidad: <b><?php if ($guardian->getDisponibilidad()) {
                                                                            echo implode(", ", $guardian->getDisponibilidad());
                                                                        } else {
                                                                            echo "Sin definir.";
                                                                        } ?></b></p>
                                <p class="card-text">Tamaño de Mascota: <b><?php if ($guardian->getTamanioMascotaCuidar()) {
                                                                                echo implode(", ", $guardian->getTamanioMascotaCuidar());
                                                                            } else {
                                                                                echo "Sin definir.";
                                                                            } ?></b></p>
                                <div class="text-end">
                                    <a href="<?php echo FRONT_ROOT ?>Reserva/ShowAddReservaView/<?php echo $guardian->getId(); ?>"><button type="button" class="btn btn-lg btn-outline-primary position-absolute bottom-0 end-0 m-3">Reservar</button></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        <?php }
        } ?>
    </div>
</div>

<?php
include("footer.php");
?>