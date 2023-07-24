<?php
/**
 * Created by PhpStorm.
 * User: Usuario
 * Date: 12/09/2018
 * Time: 11:46
 */

class Resultado
{
    public function __construct()
    {
        ORM::configure('mysql:host='.LOCAL.';dbname='.BDD);
        ORM::configure('username', USUARIO);
        ORM::configure('password', CLAVE);
    }

    public function getResultado()
    {
        $usuario = $_SESSION['email'];

        $query = ORM::for_table('nota')
            -> where('usuario', $usuario)
            -> find_many();
        return $query;
    }
}