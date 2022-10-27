<?php

use DAO\DuenioDAO;
use DAO\GuardianDAO;
use DAO\MascotaDAO;
use Models\Reserva;
use Models\Guardian;
use Models\Mascota;
use Models\EstadoReserva;

include("header.php");
include("navBar.php");
?>

<div class="container">
    <div class="list-reservas">
        <h2 id="list-title">Reservas</h2><br>
        <?php foreach ($listaReservas as $reserva) { 
            $guardianDAO = new GuardianDAO();
            $duenioDAO = new DuenioDAO();
            $mascotaDAO = new MascotaDAO();

            $guardian = $guardianDAO->BuscarId($reserva->getFkIdGuardian());
            $duenio = $duenioDAO->BuscarId($reserva->getFkIdDuenio());
            $mascota = $mascotaDAO->GetMascotaById($reserva->getFkIdMascota());
            ?>

            <div class="card mb-3 shadow-sm">
                <div class="row g-0">
                    <div class="col-md-4 card-img-reserva">
                        <img src="<?php echo IMG_PATH . $mascota->getRutaFoto() ?>" class="img-fluid rounded-start img-reserva">
                    </div>
                    <div class="col-md-8 p-1 position-relative">
                        <div class="card-body">
                            <h3 class="card-title"><b><?php echo "Reserva para " . $mascota->getNombre(); ?></b><span class="<?php echo ($reserva->getEstado() == "En curso") ? "text-primary" : "" ?>"> (<?php echo $reserva->getEstado(); ?>)</span></h3>
                            <h5><small class="card-text">desde el <b><?php echo $reserva->getFechaInicio() ?></b> hasta el <b><?php echo $reserva->getFechaFin(); ?></b></small></h5>
                            <hr class="my-3"/>
                            <?php if($_SESSION["loggedUser"]->getTipo() == 1){ ?>
                                <p class="card-text">Guardian: <b><?php echo $guardian->getNombre() . " " . $guardian->getApellido(); ?></b></p>
                                <p class="card-text">Dirección: <b><?php echo $guardian->getCalle() . " " . $guardian->getNumero() . " " . $guardian->getPiso() . " " . $guardian->getDepartamento() ?></b></p>
                            <?php } else {?>
                                <p class="card-text">Dueño: <b><?php echo $duenio->getNombre() . " " . $duenio->getApellido(); ?></b></p>
                                <p class="card-text"><span>Animal: <b><?php echo $mascota->getAnimal() ?></b></span><span class="ms-3">Raza: <b><?php echo $mascota->getRaza() ?></b></span></p>
                            <?php } ?>
                            <p class="card-text">Precio Total: <b><?php echo "$" . $reserva->getPrecioTotal(); ?></b></p>
                            <div class="text-end">
                                <?php if($_SESSION["loggedUser"]->getTipo() == 2){ ?>
                                    <form action="<?php echo FRONT_ROOT ?>Reserva/Confirm" method="Post">
                                        <input type="hidden" name="idReserva" value="<?php echo $reserva->getIdReserva(); ?>">
                                        <button type="submit" class="btn btn-lg btn-outline-success rounded-pill position-absolute bottom-0 m-2 btn-confirmar">Confirmar</button>
                                    </form>
                                <?php } ?>
                                <form action="<?php echo FRONT_ROOT ?>Reserva/Cancel" method="Post">
                                    <input type="hidden" name="idReserva" value="<?php echo $reserva->getIdReserva(); ?>">
                                    <button type="submit" class="btn btn-lg btn-outline-danger rounded-pill position-absolute bottom-0 end-0 m-2">Cancelar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <?php } ?>
    </div>
</div>

<?php
include("footer.php");
?>