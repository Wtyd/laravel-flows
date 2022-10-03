<?php

namespace Tests\Utils\CustomFaker\Persona;

class Persona
{
    public $nombre;
    public $primerApellido;
    public $segundoApellido;
    public $dni;
    public $email;
    public $sexo;

    public function __construct(string $nombre, $primerApellido, $segundoApellido, $dni, $email, $sexo, $telefono)
    {
        $this->nombre = $nombre;
        $this->primerApellido = $primerApellido;
        $this->segundoApellido = $segundoApellido;
        $this->dni = $dni;
        $this->email = $email;
        $this->sexo = $sexo;
        $this->telefono = $telefono;
    }

    public function nombre()
    {
        return $this->nombre;
    }

    public function primerApellido()
    {
        return $this->primerApellido;
    }

    public function segundoApellido()
    {
        return $this->segundoApellido;
    }

    public function dni()
    {
        return $this->dni;
    }

    public function email()
    {
        return $this->email;
    }

    public function sexo()
    {
        return $this->sexo;
    }

    public function nombreCompleto()
    {
        return "$this->nombre $this->primerApellido $this->segundoApellido";
    }

    public function telefono()
    {
        return $this->telefono;
    }
}
