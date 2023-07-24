<?php
defined("INIT") OR die("Acceso denegado");
/**
 * Created by PhpStorm.
 * User: Usuario
 * Date: 21/08/2018
 * Time: 20:18
 */

class ErrorCap
{
    public function __construct()
    {
        ORM::configure('mysql:host='.LOCAL.';dbname='.BDD);
        ORM::configure('username', USUARIO);
        ORM::configure('password', CLAVE);
    }

    public function addError($errores)
    {
        $error = ORM::for_table('errores')->create();

        $error->usuario = $errores->usuario;
        $error->cod = $errores->codigo;
        $error->save();
    }

    public function getErrores()
    {
        $query = ORM::for_table('errores')
            ->raw_query("select * from preguntas inner join errores on preguntas.cod=errores.cod where errores.usuario = '".$_SESSION['email']."' order by rand(".time()."*".time().") limit 100")
            ->find_many();
        return $query;
    }
    public function borrarErrores($errores)
    {
        $person = ORM::for_table('errores')
            ->where('usuario', $_SESSION['email'])
            ->where_in('cod', $errores)
            ->delete_many();
    }
}