<?php
defined("INIT") OR die("Acceso denegado");
/**
 * Created by PhpStorm.
 * User: Usuario
 * Date: 30/08/2018
 * Time: 12:16
 */

class Login
{
    public function __construct()
    {
        ORM::configure('mysql:host='.LOCAL.';dbname='.BDD);
        ORM::configure('username', USUARIO);
        ORM::configure('password', CLAVE);
    }
    public function retornarCod()
    {
        $usuario = $_SESSION['email'];
        $person = ORM::for_table('usuario')->where('usuario', $usuario)->find_one();
        if (!$person->cod1) {
            $aleatorio = mt_rand(1, 999999);
            $person->cod1 = $aleatorio;
            $person->save();
            return $aleatorio;
        } else {
            return $person->cod1;
        }
    }
    public function actualizarRnd($aleatorio)
    {
        $usuario = $_SESSION['email'];
        $acceso = ORM::for_table('usuario')->where('usuario', $usuario)->find_one();
        $acceso->claveP = $aleatorio;
        $acceso->save();
        return $acceso->id();
    }

    public function borrarRegistro($email)
    {
        ORM::for_table('usuario')
            ->where_equal('usuario', $email)
            ->delete_many();
    }
    public function comprobarActivacion($id, $key)
    {
        $person = ORM::for_table('usuario')->where('id', $id)->find_one();
        if (!empty($person)) {
            $claveP = $person->claveP;
            if ($claveP==$key){
                //activamos
                $persona = ORM::for_table('usuario')->where('id', $id)->find_one();
                $persona->activo = true;
                $persona->claveP = 0;
                $persona->save();
                return true;
            }
        }
        return false;
    }
    public function bajaDefinitiva($id, $key){
        $person = ORM::for_table('usuario')->where('id', $id)->find_one();
        if (!empty($person)) {
            $claveP = $person->claveP;
            $usuario = $person->usuario;
            if ($claveP==$key){
                ORM::for_table('usuario')
                    ->where_equal('usuario', $usuario)
                    ->delete_many();
                ORM::for_table('nota')
                    ->where_equal('usuario', $usuario)
                    ->delete_many();
                ORM::for_table('errores')
                    ->where_equal('usuario', $usuario)
                    ->delete_many();
                return true;
            }
        }
        return false;
    }
    public function altaEmail($user, $password, $key)
    {
        $usuario = ORM::for_table('usuario')->create();
        $usuario->usuario = $user;
        $usuario->clave = md5($password);
        $usuario->activo = false;
        $usuario->claveP = $key;
        $usuario->save();
        return $usuario->id();
    }
    public function comprobarUsuario($usuario)
    {
        $person = ORM::for_table('usuario')->where('usuario', $usuario)->find_one();
        if (!empty($person)){
            return true;
        }
        return false;
    }

    public function comprobarAcceso($usuario, $password)
    {
        $ip = $this->retornarIp();
        return $this->estasBaneado($ip, $usuario, $password);
    }

    private function retornarIp()
    {
        $ip = '';
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        };
        //return '192.12.12.14';
        return $ip;
    }

    /**
     * Si existe el registro y no supera 1 día desde el último acceso y tienes más de 3 intentos
     * estas baneado
     */
    private function estasBaneado($ip, $usuario, $password)
    {
        $person = ORM::for_table('acceso')->where('ip', $ip)->find_one();

        if (!empty($person)) {
            if ($person->intentos>3) {
                if (time()-strtotime($person->time)<86400) {
                    return false;
                } else {
                    $this->eliminarRegistro($ip);
                    return $this->comprobarLogin($ip, $usuario, $password, $person->intentos, false);
                }
            } else {
                // sumo +1
                return $this->comprobarLogin($ip, $usuario, $password, $person->intentos, true);
            }
        } else {
            //Creo el registro
            return $this->comprobarLogin($ip, $usuario, $password, 0, false);
        }
    }

    private function comprobarLogin($ip, $usuario, $password, $intentos, $existe)
    {
        $person = ORM::for_table('usuario')->where('usuario', $usuario)->find_one();

        if (!(empty($person)) && $person->clave===md5($password) && $person->activo){
            $this->eliminarRegistro($ip);
            $_SESSION['id'] = $person->id;
            return true;
        }

        if ($existe) {
            $this->sumarIntentos($ip, $intentos);
        } else {
            $this->crearIntentos($ip);
        }
        return false;
    }

    private function eliminarRegistro($ip)
    {
        ORM::for_table('acceso')
            ->where_equal('ip', $ip)
            ->delete_many();
    }
    private function sumarIntentos($ip, $intentos)
    {
        $acceso = ORM::for_table('acceso')->where('ip', $ip)->find_one();
        $acceso->intentos = $intentos + 1;
        $acceso->save();
    }
    private function crearIntentos($ip)
    {
        $acceso = ORM::for_table('acceso')->create();
        $acceso->ip = $ip;
        $acceso->intentos = 1;
        $acceso->save();
    }
    public function clearIP()
    {
        $fecha = date('Y/m/d H:i:s', strtotime('-1 day'));
        $person = ORM::for_table('acceso')
            ->where_lt('time', $fecha)
            ->delete_many();
    }
}