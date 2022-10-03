<?php

namespace Tests\Utils\CustomFaker\Persona;

use Faker\Provider\Base;

class PersonaProvider extends Base
{
    protected $faker;
    protected $nombreCompleto;
    protected $nombre;
    protected $primerApellido;
    protected $segundoApellido;
    protected $dni;
    protected $email;
    protected $sexo;

    public function persona()
    {
        $sexo = static::randomElement(['male', 'female']);

        $this->nombre = $this->generator->firstName($sexo);
        $this->primerApellido = $this->generator->lastName();
        $this->segundoApellido = $this->generator->lastName();
        $this->nombreCompleto = "$this->nombre $this->primerApellido $this->segundoApellido";
        $this->dni = $this->generator->dni();
        $this->email = $this->emailPersonal($this->generator->email());
        $this->sexo = $sexo === 'male' ? 'Hombre' : 'Mujer';

        return new Persona(
            $this->nombre,
            $this->primerApellido,
            $this->segundoApellido,
            $this->dni,
            $this->email,
            $this->sexo,
            $this->generator->customMobileNumber()
        );
    }

    protected function emailPersonal(string $emailOriginal)
    {
        $separacion = explode('@', $emailOriginal);

        $numero = '';
        if (rand(1, 100) > 50) {
            $numero = $this->generator->numerify('##');
        }

        switch (rand(1, 5)) {
            case 1:
                // NombrePrimerApellidoSegundoApellido
                $separacion[0] = "$this->nombre$this->primerApellido$this->segundoApellido$numero";
                break;
            case 2:
                // Nombre.PrimerApellido.SegundoApellido
                $separacion[0] = "$this->nombre.$this->primerApellido.$this->segundoApellido$numero";
                break;

            case 3:
                // NombrePrimerApellido
                $separacion[0] = "$this->nombre.$this->primerApellido$numero";
                break;

            case 4:
                // NPrimerApellidoSegundoApellido
                $separacion[0] = $this->nombre[0] . "$this->primerApellido$this->segundoApellido$numero";
                break;

            default:
                // NombrePrimerApellidoSeg
                $separacion[0] = "$this->nombre$this->primerApellido" . substr($this->segundoApellido, 0, rand(1, 3)) . $numero;
                break;
        }

        $separacion[0] = strtolower(str_replace(' ', '', $separacion[0])); //filtros -> quita espacios en blanco y en minuscula
        $separacion[0] = $this->stripAccents($separacion[0]); // Elimina los acentos
        return implode('@', $separacion);
    }

    /**
     * Elimina los acentos y los carácteres extraños
     *
     * @param string $str
     * @return string
     */
    protected function stripAccents($str): string
    {
        return strtr(utf8_decode($str), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
    }
}
