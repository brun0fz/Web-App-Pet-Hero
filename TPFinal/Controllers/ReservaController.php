<?php

namespace Controllers;

use DAO\DuenioDAO;
use DAO\GuardianDAO;
use DAO\MascotaDAO;
use DAO\ReservaDAO;
use DateTime;
use Exception;
use Models\Cupon;
use Models\Duenio;
use Models\EstadoReserva;
use Models\Reserva;
use Models\Review;

class ReservaController
{

    private $mascotaDAO;
    private $guardianDAO;
    private $reservaDAO;
    private $duenioDAO;

    public function __construct()
    {
        $this->mascotaDAO = new MascotaDAO();
        $this->guardianDAO = new GuardianDAO();
        $this->reservaDAO = new ReservaDAO();
        $this->duenioDAO = new DuenioDAO();
    }

    public function ShowAddReservaView($idGuardian, $fechaInicio, $fechaFin, $idMascota)
    {
        if (isset($_SESSION["loggedUser"]) && ($_SESSION["loggedUser"]->getTipo() == 1)) {
            try {
                $guardian = $this->guardianDAO->GetGuardianById($idGuardian);

                $precioTotal = $this->CalcularPrecioTotal($fechaInicio, $fechaFin, $guardian->getPrecioXDia());

                $mascota = $this->mascotaDAO->GetMascotaById($idMascota);
                require_once(VIEWS_PATH . "add-reserva.php");
            } catch (Exception $ex) {
                HomeController::ShowErrorView();
            }
        } else {
            HomeController::Index();
        }
    }

    public function ShowListReservasView($alert = "")
    {
        if (isset($_SESSION["loggedUser"])) {
            try {
                if ($_SESSION["loggedUser"]->getTipo() == 1) {
                    $listaReservas = array();
                    $listaReservas = array_merge($listaReservas, $this->reservaDAO->GetListaReservasDuenioByEstado($_SESSION["loggedUser"]->getId(), EstadoReserva::EN_CURSO->value));
                    $listaReservas = array_merge($listaReservas, $this->reservaDAO->GetListaReservasDuenioByEstado($_SESSION["loggedUser"]->getId(), EstadoReserva::ESPERA->value));
                    $listaReservas = array_merge($listaReservas, $this->reservaDAO->GetListaReservasDuenioByEstado($_SESSION["loggedUser"]->getId(), EstadoReserva::CONFIRMADA->value));
                    $listaReservas = array_merge($listaReservas, $this->reservaDAO->GetListaReservasDuenioByEstado($_SESSION["loggedUser"]->getId(), EstadoReserva::SOLICITADA->value));
                    $listaReservas = array_merge($listaReservas, $this->reservaDAO->GetListaReservasDuenioByEstado($_SESSION["loggedUser"]->getId(), EstadoReserva::FINALIZADA->value));
                    $listaReservas = array_merge($listaReservas, $this->reservaDAO->GetListaReservasDuenioByEstado($_SESSION["loggedUser"]->getId(), EstadoReserva::CANCELADA->value));
                } else {
                    $listaReservas = array();
                    $listaReservas = array_merge($listaReservas, $this->reservaDAO->GetListaReservasGuardianByEstado($_SESSION["loggedUser"]->getId(), EstadoReserva::EN_CURSO->value));
                    $listaReservas = array_merge($listaReservas, $this->reservaDAO->GetListaReservasGuardianByEstado($_SESSION["loggedUser"]->getId(), EstadoReserva::SOLICITADA->value));
                    $listaReservas = array_merge($listaReservas, $this->reservaDAO->GetListaReservasGuardianByEstado($_SESSION["loggedUser"]->getId(), EstadoReserva::ESPERA->value));
                    $listaReservas = array_merge($listaReservas, $this->reservaDAO->GetListaReservasGuardianByEstado($_SESSION["loggedUser"]->getId(), EstadoReserva::CONFIRMADA->value));
                    $listaReservas = array_merge($listaReservas, $this->reservaDAO->GetListaReservasGuardianByEstado($_SESSION["loggedUser"]->getId(), EstadoReserva::FINALIZADA->value));
                    $listaReservas = array_merge($listaReservas, $this->reservaDAO->GetListaReservasGuardianByEstado($_SESSION["loggedUser"]->getId(), EstadoReserva::CANCELADA->value));
                }

                require_once(VIEWS_PATH . "list-reservas.php");
            } catch (Exception $ex) {
                HomeController::ShowErrorView();
            }
        } else {
            HomeController::Index();
        }
    }

    public function ShowCuponView($idReserva)
    {
        if (isset($_SESSION["loggedUser"]) && ($_SESSION["loggedUser"]->getTipo() == 1)) {
            try {
                $cupon = $this->reservaDAO->GetCuponByIdReserva($idReserva);
                $reserva = $this->reservaDAO->GetReservaById($cupon->getFkIdReserva());
                $guardian = $this->guardianDAO->GetGuardianById($reserva->getFkIdGuardian());
                $mascota = $this->mascotaDAO->GetMascotaById($reserva->getFkIdMascota());
                require_once(VIEWS_PATH . "show-cupon.php");
            } catch (Exception $ex) {
                HomeController::ShowErrorView();
            }
        } else {
            HomeController::Index();
        }
    }

    public function ShowReviewView($idReserva)
    {

        if (isset($_SESSION["loggedUser"]) && ($_SESSION["loggedUser"]->getTipo() == 1)) {
            try {
                $reserva = $this->reservaDAO->GetReservaById($idReserva);
                $guardian = $this->guardianDAO->GetGuardianById($reserva->getFkIdGuardian());
                $duenio = $this->duenioDAO->GetDuenioById($reserva->getFkIdDuenio());
                $mascota = $this->mascotaDAO->GetMascotaById($reserva->getFkIdMascota());

                require_once(VIEWS_PATH . "add-review.php");
            } catch (Exception $ex) {
                HomeController::ShowErrorView();
            }
        } else {
            HomeController::Index();
        }
    }

    public function Add($fechaInicio, $fechaFin, $precioTotal, $idMascota, $idGuardian, $idDuenio)
    {
        if (isset($_SESSION["loggedUser"]) && ($_SESSION["loggedUser"]->getTipo() == 1)) {
            try {
                $reserva = new Reserva($fechaInicio, $fechaFin, $precioTotal, $idMascota, $idDuenio, $idGuardian);

                $this->reservaDAO->Add($reserva);

                $alert = "Reserva realizada con &eacute;xito.";

                $this->ShowListReservasView($alert);
            } catch (Exception $ex) {
                HomeController::ShowErrorView();
            }
        } else {
            HomeController::Index();
        }
    }

    private function CalcularPrecioTotal($fechaInicio, $fechaFin, $precioXDia)
    {
        $dateInicio = new DateTime($fechaInicio);
        $dateFin = new DateTime($fechaFin);

        $difference = $dateInicio->diff($dateFin);

        $dias = 1 + (int) $difference->format("%d days ");

        return $dias * $precioXDia;
    }

    public function cambiarEstado($idReserva, $estado)
    {
        if (isset($_SESSION["loggedUser"])) {
            try {
                $this->reservaDAO->UpdateEstado($idReserva, $estado);

                switch ($estado) {
                    case EstadoReserva::CANCELADA->value:

                        if ($_SESSION["loggedUser"]->getTipo() == 2) {
                            $reserva = $this->reservaDAO->GetReservaById($idReserva);
                            $duenio = $this->duenioDAO->GetDuenioById($reserva->getFkIdDuenio());
                            MailController::MailCancelarReserva($duenio, $reserva);
                        }

                        $alert = "La reserva ha sido cancelada.";
                        break;
                    case EstadoReserva::CONFIRMADA->value:
                        $alert = "Su pago ha sido realizado con exito.";
                        break;
                    case EstadoReserva::SOLICITADA->value:
                        $alert = "Su reserva ha sido solicitada. Espere confimarcion del Guardian.";
                        break;
                }

                $this->ShowListReservasView($alert);
            } catch (Exception $ex) {
                HomeController::ShowErrorView();
            }
        } else {
            HomeController::Index();
        }
    }

    public function confirmarReserva($idReserva)
    {
        if (isset($_SESSION["loggedUser"]) && $_SESSION["loggedUser"]->getTipo() == 2) {
            try {
                $this->reservaDAO->UpdateEstado($idReserva, EstadoReserva::ESPERA->value);
                $alert = "Se ha enviado el cupon de pago.";

                $reservaConfirmada = $this->reservaDAO->GetReservaById($idReserva);

                ///EMAIL
                $duenio = $this->duenioDAO->GetDuenioById($reservaConfirmada->getFkIdDuenio());
                $guardian = $this->guardianDAO->GetGuardianById($reservaConfirmada->getFkIdGuardian());

                $mascotaConfirmada = $this->mascotaDAO->GetMascotaById($reservaConfirmada->getFkIdMascota());

                $reservasSolicitadas = $this->reservaDAO->GetListaReservasGuardianByEstado($_SESSION["loggedUser"]->getId(), EstadoReserva::SOLICITADA->value);

                $diasReservaConfirmada = $this->GetDiasReserva($reservaConfirmada->getFechaInicio(), $reservaConfirmada->getFechaFin());

                foreach ($reservasSolicitadas as $reserva) {

                    $interseccionDias = array();

                    $mascota = $this->mascotaDAO->GetMascotaById($reserva->getFkIdMascota());

                    $diasReserva = $this->GetDiasReserva($reserva->getFechaInicio(), $reserva->getFechaFin());

                    $interseccionDias = array_intersect($diasReservaConfirmada, $diasReserva);

                    if (!empty($interseccionDias)) {
                        if ($mascota->getAnimal() != $mascotaConfirmada->getAnimal() || $mascota->getRaza() != $mascotaConfirmada->getRaza()) {
                            $this->reservaDAO->UpdateEstado($reserva->getIdReserva(), EstadoReserva::CANCELADA->value);
                            MailController::MailCancelarReserva($duenio, $reserva);
                        }
                    }
                }

                ///Cupon de pago
                $precioParcial = ($reservaConfirmada->getPrecioTotal() * 0.5);

                $cupon = new Cupon($idReserva, $precioParcial);
                $this->reservaDAO->AddCupon($cupon);

                ///EMAIL
                $duenio = $this->duenioDAO->GetDuenioById($reservaConfirmada->getFkIdDuenio());
                $guardian = $this->guardianDAO->GetGuardianById($reservaConfirmada->getFkIdGuardian());

                MailController::MailCupon($duenio, $reservaConfirmada, $mascotaConfirmada, $guardian);

                $this->ShowListReservasView($alert);
            } catch (Exception $ex) {
                HomeController::ShowErrorView();
            }
        } else {
            HomeController::Index();
        }
    }


    private function GetDiasReserva($fechaInicio, $fechaFin)
    {

        $timeInicio = strtotime($fechaInicio);
        $timeFin = strtotime($fechaFin);

        while ($timeInicio <= $timeFin) {

            $dias[] = (date("Y-m-d", $timeInicio));

            $timeInicio += 86400;
        }

        return $dias;
    }

    public function AddReview($comentario, $puntaje, $idReserva)
    {
        if (isset($_SESSION["loggedUser"]) && ($_SESSION["loggedUser"]->getTipo() == 1)) {
            try {
                $review = new Review($comentario, $puntaje, $idReserva);
                $this->reservaDAO->AddReview($review);

                $this->guardianDAO->UpdateReputacion($idReserva);

                $alert = "Su calificaci&oacute;n ha sido enviada.";

                $this->ShowListReservasView($alert);
            } catch (Exception $ex) {
                HomeController::ShowErrorView();
            }
        } else {
            HomeController::Index();
        }
    }

    public function PagarCupon($metodoPago, $nombre, $numero, $vencimiento, $cvv, $idReserva, $estado)
    {
        //Los datos de pago no son utilizados ya que esto es solo una simulacion de pago
        if (isset($_SESSION["loggedUser"]) && ($_SESSION["loggedUser"]->getTipo() == 1)) {
            try {
                $this->reservaDAO->UpdateEstado($idReserva, $estado);
                $alert = "Cup&oacute;n pagado con &eacute;xito. La reserva ha sido confirmada.";

                $this->ShowListReservasView($alert);
            } catch (Exception $ex) {
                HomeController::ShowErrorView();
            }
        } else {
            HomeController::Index();
        }
    }

}
