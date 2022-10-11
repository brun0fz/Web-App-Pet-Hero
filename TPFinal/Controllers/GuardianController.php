<?php

namespace Controllers;

use DAO\GuardianDAO;
use Models\Guardian;

class GuardianController
{
    private $guardianDAO;

    public function __construct()
    {
        $this->guardianDAO = new GuardianDAO();
    }

    private function validateSession()
    {
        if (isset($_SESSION["loggedUser"]) && $_SESSION["loggedUser"]->getTipo() == 2) {
            return true;
        } else {
            HomeController::Index();
        }

    }

    public function ShowGuardianHome()
    {
        $this->validateSession() && require_once(VIEWS_PATH . "guardianHome.php");
    }

    public function ShowDisponibilidadView()
    {
        if ($this->validateSession()) {
            require_once(VIEWS_PATH . "set-disponibilidad.php");
        }
    }

    public function Add($nombre, $apellido, $telefono, $email, $password, $direccion)
    {
        if ($this->validateSession()) {
            $guardian = new Guardian($nombre, $apellido, $telefono, $email, $password, $direccion);
            $this->guardianDAO->Add($guardian);

            $_SESSION["loggedUser"] = $guardian;
            $this->ShowGuardianHome();
        }
    }

    public function setDisponibilidad($dias)
    {
        if ($this->validateSession()) {
            $_SESSION["loggedUser"]->setDisponibilidad($dias);
            $this->guardianDAO->UpdateDisponibilidad($dias, $_SESSION["loggedUser"]);
            $this->ShowDisponibilidadView();
        }
    }

}
