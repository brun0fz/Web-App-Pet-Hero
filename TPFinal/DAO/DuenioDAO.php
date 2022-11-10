<?php

namespace DAO;

use \Exception as Exception;
use DAO\IDuenioDAO as IDuenioDAO;
use Models\Duenio as Duenio;
use DAO\Connection as Connection;

class DuenioDAO implements IDuenioDAO
{
    private $connection;
    private $tableName = "Duenios";

    public function Add(Duenio $duenio)
    {
        try {
            $query = "INSERT INTO " . $this->tableName . " (nombre, apellido, telefono,email, password, tipo, rutaFoto, alta) VALUES (:nombre, :apellido, :telefono, :email, aes_encrypt(:password, :encryptpass), :tipo, :rutaFoto, :alta);";

            $parameters["encryptpass"] = ENCRYPTPASS;
            $parameters["nombre"] = $duenio->getNombre();
            $parameters["apellido"] = $duenio->getApellido();
            $parameters["telefono"] = $duenio->getTelefono();
            $parameters["email"] = $duenio->getEmail();
            $parameters["password"] = $duenio->getPassword();
            $parameters["tipo"] = $duenio->getTipo();
            $parameters["rutaFoto"] = $duenio->getRutaFoto();
            $parameters["alta"] = $duenio->getAlta();

            $this->connection = Connection::GetInstance();

            $this->connection->ExecuteNonQuery($query, $parameters);
            
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function GetAll()
    {
        try {
            $duenioList = array();

            $query = "SELECT * FROM " . $this->tableName;

            $this->connection = Connection::GetInstance();

            $resultSet = $this->connection->Execute($query);

            foreach ($resultSet as $row) {

                $duenio = new Duenio(NULL, NULL, NULL, NULL, NULL);

                $duenio->setId($row["idDuenio"]);
                $duenio->setNombre($row["nombre"]);
                $duenio->setApellido($row["apellido"]);
                $duenio->setTelefono($row["telefono"]);
                $duenio->setEmail($row["email"]);
                $duenio->setTipo($row["tipo"]);
                $duenio->setRutaFoto($row["rutaFoto"]);
                $duenio->setAlta($row["alta"]);

                array_push($dueniosList, $duenio);
            }

            return $dueniosList;
        } catch (Exception $ex) {
            throw $ex;
        }
    }


    public function GetDuenioByEmail($email)
    {
        try {
            $duenio = NULL;

            $query = "SELECT *, aes_decrypt(password, :encryptpass) as password FROM " . $this->tableName . " WHERE (email = :email)";

            $parameters["email"] = $email;
            $parameters["encryptpass"] = ENCRYPTPASS;

            $this->connection = Connection::GetInstance();

            $resultSet = $this->connection->Execute($query, $parameters);

            if (isset($resultSet)) {

                foreach ($resultSet as $row) {

                    $duenio = new Duenio(NULL, NULL, NULL, NULL, NULL);

                    $duenio->setId($row["idDuenio"]);
                    $duenio->setNombre($row["nombre"]);
                    $duenio->setApellido($row["apellido"]);
                    $duenio->setTelefono($row["telefono"]);
                    $duenio->setEmail($row["email"]);
                    $duenio->setPassword($row["password"]); //Se usa para el login
                    $duenio->setTipo($row["tipo"]);
                    $duenio->setRutaFoto($row["rutaFoto"]);
                    $duenio->setAlta($row["alta"]);
                }

                return $duenio;
            } else {

                return null;
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function GetDuenioById($idDuenio)
    {
        try {
            $duenio = NULL;

            $query = "SELECT * FROM " . $this->tableName . " WHERE (idDuenio = :idDuenio)";

            $parameters["idDuenio"] = $idDuenio;

            $this->connection = Connection::GetInstance();

            $resultSet = $this->connection->Execute($query, $parameters);

            if (isset($resultSet)) {

                foreach ($resultSet as $row) {

                    $duenio = new Duenio(NULL, NULL, NULL, NULL, NULL);

                    $duenio->setId($row["idDuenio"]);
                    $duenio->setNombre($row["nombre"]);
                    $duenio->setApellido($row["apellido"]);
                    $duenio->setTelefono($row["telefono"]);
                    $duenio->setEmail($row["email"]);
                    $duenio->setTipo($row["tipo"]);
                    $duenio->setRutaFoto($row["rutaFoto"]);
                    $duenio->setAlta($row["alta"]);
                }

                return $duenio;
            } else {

                return null;
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
