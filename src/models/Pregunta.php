<?php
defined("INIT") OR die("Acceso denegado");
/**
 * Created by PhpStorm.
 * User: Usuario
 * Date: 19/07/2018
 * Time: 20:57
 */

class Pregunta
{
    public function __construct()
    {
        ORM::configure('mysql:host='.LOCAL.';dbname='.BDD);
        ORM::configure('username', USUARIO);
        ORM::configure('password', CLAVE);
    }

    public function getTest($materia = '', $numeroPreguntas = 100, $tipo = '')
    {
        $arrayCod = array();
        $aleatorio;
        $tipo_where;

        switch ($materia) {
            case 'viajero-todas':
                $tipo_where = array(
                    array('tipo' => 'v1'),
                    array('tipo' => 'v2'),
                    array('tipo' => 'v3')
                );
                break;
            case 'viajero-conduccion-racional':
                $tipo_where = array(array('tipo' => 'v1'));
                break;
            case 'viajero-aplicacion-reglamento':
                $tipo_where = array(array('tipo' => 'v2'));
                break;
            case 'viajero-salud-seguridad-logistica':
                $tipo_where = array(array('tipo' => 'v3'));
                break;

            case 'mercancia-todas':
                $tipo_where = array(
                    array('tipo' => 'm1'),
                    array('tipo' => 'm2'),
                    array('tipo' => 'm3')
                );
                break;
            case 'mercancia-conduccion-racional':
                $tipo_where = array(array('tipo' => 'm1'));
                break;
            case 'mercancia-aplicacion-reglamento':
                $tipo_where = array(array('tipo' => 'm2'));
                break;
            case 'mercancia-salud-seguridad-logistica':
                $tipo_where = array(array('tipo' => 'm3'));
                break;

            case 'comunes-todas':
                $tipo_where = array(
                    array('tipo' => 'c1'),
                    array('tipo' => 'c2'),
                    array('tipo' => 'c3')
                );
                break;
            case 'comunes-conduccion-racional':
                $tipo_where = array(array('tipo' => 'c1'));
                break;
            case 'comunes-aplicacion-reglamento':
                $tipo_where = array(array('tipo' => 'c2'));
                break;
            case 'comunes-salud-seguridad-logistica':
                $tipo_where = array(array('tipo' => 'c3'));
                break;
        }

        // este nuevo algoritmo es mucho más rápido; aunque tiene que ser consecutivos los tipos (c,v,c...) y no haber
        // saltos en los id. El anterior algoritmo sin embargo sirve para cualquier caso, aunque es menos eficiente
        // Los mínimos y máximos realmente ya lo sabemos: el m(del 1 al 1877), el v(del 1878 al 3105) y el c(3106-8145);
        // pero podrían cambiar con alguna actualización
        $min = ORM::for_table('preguntas')
            ->where_any_is($tipo_where)
            ->min('id');
        $max = ORM::for_table('preguntas')
            ->where_any_is($tipo_where)
            ->max('id');

        for ($i = 1; $i <= $numeroPreguntas; $i++) {
            $aleatorio = mt_rand($min, $max);
            while (in_array($aleatorio, $arrayCod)) {
                $aleatorio = mt_rand($min, $max);
            }
            $arrayCod[] = $aleatorio;
        }

        $query = ORM::for_table('preguntas')->where_in('id', $arrayCod)->find_many();
        return $query;
    }

    /*********************************************************************
     * es un simulacro...las primeras 25 preguntas son específicas y el resto comunes
     **************************************************************************/
    public function getSimulacro($materia = '', $tipo = '')
    {
        $arrayCod = array();
        $aleatorio;
        $tipo_where;

        switch ($materia) {
            case 'mercancia':
                $tipo_where = array(
                    array('tipo' => 'm1'),
                    array('tipo' => 'm2'),
                    array('tipo' => 'm3')
                );
                break;
            case 'viajero':
                $tipo_where = array(
                    array('tipo' => 'v1'),
                    array('tipo' => 'v2'),
                    array('tipo' => 'v3')
                );
                break;
            default:
                $tipo_where = array(
                    array('tipo' => 'm1'),
                    array('tipo' => 'm2'),
                    array('tipo' => 'm3')
                );
        }

        # Primeras 25 preguntas específicas; las 75 restantes comunes
        $min = ORM::for_table('preguntas')
            ->where_any_is($tipo_where)
            ->min('id');
        $max = ORM::for_table('preguntas')
            ->where_any_is($tipo_where)
            ->max('id');

        for ($i = 1; $i <= 25; $i++) {
            $aleatorio = mt_rand($min, $max);
            while (in_array($aleatorio, $arrayCod)) {
                $aleatorio = mt_rand($min, $max);
            }
            $arrayCod[] = $aleatorio;
        }

        # Ahora vamos por las 75 preguntas restantes que son las relativas a comunes
        $tipo_where = array(
            array('tipo' => 'c1'),
            array('tipo' => 'c2'),
            array('tipo' => 'c3')
        );
        $min = ORM::for_table('preguntas')
            ->where_any_is($tipo_where)
            ->min('id');
        $max = ORM::for_table('preguntas')
            ->where_any_is($tipo_where)
            ->max('id');

        for ($i = 1; $i <= 75; $i++) {
            $aleatorio = mt_rand($min, $max);
            while (in_array($aleatorio, $arrayCod)) {
                $aleatorio = mt_rand($min, $max);
            }
            $arrayCod[] = $aleatorio;
        }

        $query = ORM::for_table('preguntas')->where_in('id', $arrayCod)->find_many();
        return $query;
    }

    public function getSimulacroExamenes($nombre, $tipo)
    {
        $arrayCod = array();
        if ($tipo == 'mercancia') {
            $ruta = ROOT_DIR.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'test-reales'.DIRECTORY_SEPARATOR.'mercancia';
        } else {
            $ruta = ROOT_DIR.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'test-reales'.DIRECTORY_SEPARATOR.'viajero';
        }

        $archivo = $ruta.DIRECTORY_SEPARATOR.$nombre.'.txt';

        /**
         * Abrimos el archivo para incluir las preguntas en la consulta
         */
        $handle = @fopen($archivo, "r") or die("No se pudo abrir el archivo");
        while (!feof($handle)) {
            $arrayCod[] = trim(fgets($handle));
        }
        fclose($handle);

        $query = ORM::for_table('preguntas')->where_in('cod', $arrayCod)->find_many();
        return $query;
    }

    public function getTestAntiguo($materia = 'viajero-todas', $numeroPreguntas = 100, $tipo = '')
    {
        $arrayCod = array();
        $aleatorio;
        $tipo_where;

        switch ($materia) {
            case 'viajero-todas':
                $tipo_where = array(
                    array('tipo' => 'v1'),
                    array('tipo' => 'v2'),
                    array('tipo' => 'v3')
                );
                break;
            case 'viajero-conduccion-racional':
                $tipo_where = array(array('tipo' => 'v1'));
                break;
            case 'viajero-aplicacion-reglamento':
                $tipo_where = array(array('tipo' => 'v2'));
                break;
            case 'viajero-seguridad-logistica':
                $tipo_where = array(array('tipo' => 'v3'));
                break;
        }

        $numeroFilas = ORM::for_table('preguntas')
            ->where_any_is($tipo_where)
            ->count();

        for ($i = 1; $i <= $numeroPreguntas; $i++) {
            $aleatorio = mt_rand(0, $numeroFilas - 1);
            while (in_array($aleatorio, $arrayCod)) {
                $aleatorio = mt_rand(0, $numeroFilas - 1);
            }

            $query = ORM::for_table('preguntas')
                ->where_any_is($tipo_where)
                ->limit(1)
                ->offset($aleatorio - 1)
                ->find_one();
            $arrayCod[] = (int)$query->cod;
        }

        $query = ORM::for_table('preguntas')->where_in('cod', $arrayCod)->find_many();
        return $query;
    }

    public function getSimulacroAntiguo($materia = 'mercancia', $tipo = '')
    {
        $arrayCod = array();
        $aleatorio;
        $tipo_where;

        switch ($materia) {
            case 'mercancia':
                $tipo_where = array(
                    array('tipo' => 'm1'),
                    array('tipo' => 'm2'),
                    array('tipo' => 'm3')
                );
                break;
            case 'viajeros':
                $tipo_where = array(
                    array('tipo' => 'v1'),
                    array('tipo' => 'v2'),
                    array('tipo' => 'v3')
                );
                break;
            default:
                $tipo_where = array(
                    array('tipo' => 'm1'),
                    array('tipo' => 'm2'),
                    array('tipo' => 'm3')
                );
        }

        # Primeras 25 preguntas específicas; las 75 restantes comunes
        $numeroFilas = ORM::for_table('preguntas')
            ->where_any_is($tipo_where)
            ->count();

        for ($i = 1; $i <= 25; $i++) {
            $aleatorio = mt_rand(0, $numeroFilas - 1);
            while (in_array($aleatorio, $arrayCod)) {
                $aleatorio = mt_rand(0, $numeroFilas - 1);
            }

            $query = ORM::for_table('preguntas')
                ->where_any_is($tipo_where)
                ->limit(1)
                ->offset($aleatorio - 1)
                ->find_one();
            $arrayCod[] = (int)$query->cod;
        }

        # Ahora vamos por las 75 preguntas restantes que son las relativas a comunes
        $tipo_where = array(
            array('tipo' => 'c1'),
            array('tipo' => 'c2'),
            array('tipo' => 'c3')
        );

        $numeroFilas = ORM::for_table('preguntas')
            ->where_any_is($tipo_where)
            ->count();
        for ($i = 1; $i <= 75; $i++) {
            $aleatorio = mt_rand(0, $numeroFilas - 1);
            while (in_array($aleatorio, $arrayCod)) {
                $aleatorio = mt_rand(0, $numeroFilas - 1);
            }

            $query = ORM::for_table('preguntas')
                ->where_any_is($tipo_where)
                ->limit(1)->offset($aleatorio - 1)
                ->find_one();
            $arrayCod[] = (int)$query->cod;
        }

        $query = ORM::for_table('preguntas')->where_in('cod', $arrayCod)->find_many();
        return $query;
    }

    public function getEspecificoExamen($tipo, $clave, $numero){
        if ($tipo='mercancia') {
            $tipo = "tipo='m1' or tipo='m2' or tipo='m3' or tipo='c1' or tipo='c2' or tipo='c3'";
        } else {
            $tipo = "tipo='v1' or tipo='v2' or tipo='v3' or tipo='c1' or tipo='c2' or tipo='c3'";
        }
        $query = ORM::for_table('preguntas')
            ->raw_query("select * from preguntas where (".$tipo.") and pregunta like '%".$clave."%' order by rand(".time()."*".time().") limit ".$numero)
            ->find_many();
        return $query;

    }

    public function getPregunta($tipo, $buscar){
        $query = '';
        if ($tipo=='pregunta') {
            $query = ORM::for_table('preguntas')
                -> where_like('pregunta', '%'.$buscar.'%')
                -> limit(100)
                -> find_many();
        } else {
            $query = ORM::for_table('preguntas')
                -> where('cod', $buscar)
                -> find_many();
        }
        return $query;
    }

    # Esta función sólo es utilizada para el cálculo automático de los examenes reales
    public function getCodigo($pregunta) {
        $query = ORM::for_table('preguntas')
            -> where_like('pregunta', '%'.$pregunta.'%')
            -> limit(10)
            -> find_many();
        return $query;
    }

}