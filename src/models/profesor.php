<?php
defined("INIT") OR die("Acceso denegado");

class profesor
{
    public function __construct()
    {
        ORM::configure('mysql:host='.LOCAL.';dbname='.BDD);
        ORM::configure('username', USUARIO);
        ORM::configure('password', CLAVE);
    }

    public function grabarExamen($cod, $codigoPregunta)
    {
        $usuario = ORM::for_table('profesor')->create();
        $usuario->cod = $cod;
        $usuario->pregunta = $codigoPregunta;
        $usuario->fallo = 0;
        $usuario->acierto = 0;
        $usuario->save();
    }
}