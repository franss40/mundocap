<?php
defined("INIT") OR die("Acceso denegado");
/**
 * Created by PhpStorm.
 * User: Usuario
 * Date: 25/11/2018
 * Time: 1:14
 */

class PreguntaADR
{
    public function __construct()
    {
        ORM::configure('mysql:host='.LOCAL.';dbname='.BDD);
        ORM::configure('username', USUARIO);
        ORM::configure('password', CLAVE);
    }

    # Esta función sólo es utilizada para la adición de preguntas
    public function addTest($ask, $respA, $respB, $respC, $sol, $tipo)
    {
        $preg = ORM::for_table('preguntasadr')->create();

        $preg->pregunta = $ask;
        $preg->respA = $respA;
        $preg->respB = $respB;
        $preg->respC = $respC;
        $preg->solucion = $sol;
        $preg->tipo = $tipo;
        $preg->save();
    }
    #antes de añadir los registros hay que comprobar que no están duplicadas
    public function isAdd($ask)
    {
        $query = ORM::for_table('preguntasadr')
            -> where_like('pregunta', $ask.'%')
            -> limit(2)
            -> find_many();
        return $query;
    }
}