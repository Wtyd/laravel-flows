<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use League\Flysystem\Exception as FileNotFoundException;

class SpaController extends Controller
{
    public function index()
    {
        try {
            $index = Storage::disk('dist')->get('index.html');
            return $index;
        } catch (FileNotFoundException $exception) {
            return "<p>
            No has instalado el front-end. Ejecuta:
            </p>
             <code>yarn install && yarn build</code>
             <p>
             Si sigues teniendo problemas revisa la instalación de la aplicación en el README.md
             </p>";
        }
    }
}
