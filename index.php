<?php
session_cache_limiter(false);
session_start();

error_reporting(E_ALL);
$_SESSION['navegador'] = $_SERVER['HTTP_USER_AGENT'];

# DIRECTORIO PRINCIPAL
define('ROOT_DIR', dirname(__FILE__));

# cargamos las constantes
require ROOT_DIR.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'constantes.php';

include ROOT_DIR.'/vendor/autoload.php';
\Slim\Slim::registerAutoloader();

//$app = new \Slim\Slim();
//$app->config('debug', false) // es obligado para personalizar los errores
/* instancia y Configuración en la misma línea */
$app = new \Slim\Slim(array(
    'templates.path' => './src/templates',
    'mode' => MODO,         // test, production, development ( Default )
    'log.enable' => true,
    'debug' => DEBUG
));


/*unset($_SESSION['ocultarC']);*/
//rutas de casa, rutas de login, ....
include ROOT_DIR.'/src/frontend/home.php';
include ROOT_DIR.'/src/frontend/redirecciones.php';
include ROOT_DIR.'/src/frontend/test.php';
include ROOT_DIR.'/src/frontend/login.php';
include ROOT_DIR.'/src/frontend/adr.php';
include ROOT_DIR.'/src/frontend/crearExamen.php';
include ROOT_DIR.'/src/frontend/profesor.php';
$app->run();
