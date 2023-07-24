<?php
defined("INIT") OR die("Acceso denegado");
/**
 * Created by PhpStorm.
 * User: Usuario
 * Date: 21/08/2018
 * Time: 20:19
 */

class Nota
{
    public function __construct()
    {
        ORM::configure('mysql:host='.LOCAL.';dbname='.BDD);
        ORM::configure('username', USUARIO);
        ORM::configure('password', CLAVE);
    }

    public function addNota($resultado)
    {
        $nota = ORM::for_table('nota')->create();

        $nota->acierto = $resultado->acierto;
        $nota->fallo = $resultado->fallo;
        $nota->nula = $resultado->nula;
        $nota->reloj = $resultado->reloj;
        $nota->fecha = $resultado->fecha;
        $nota->nota = $resultado->nota;
        $nota->usuario = $resultado->usuario;
        $nota->save();
    }

    public function getNotas()
    {

    }
}