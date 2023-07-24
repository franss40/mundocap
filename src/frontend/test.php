<?php
defined("INIT") OR die("Acceso denegado");

/**
 * Middleware para zona de acceso restringuidas
 */
$mwAcceso = function ($app) {
    return function () use ($app) {
        if (empty($_SESSION['usuario'])){
            $info = "Usted no puede acceder a este sitio si no está dado de alta antes";
            $app->render('activar.phtml', array('info' => $info));
            exit;
        }
    };
};

/**
*    ruta de los test
 */

/**
 * TEST DE ERRORES
 */
$app->get('/cap/test/errores(/)', $mwAcceso($app), function () use ($app) {
    $pregunta = new ErrorCap();
    $app->render('errores.phtml', array(
        'preguntas' => $pregunta->getErrores(),
        'enlace' => 'test > errores'
    ));
});

$app->post('/cap/test/grabar-errores(/)', $mwAcceso($app), function () use ($app) {
    if (!isset($_POST)){
        $app->notFound();
    }
    $usuario = $_SESSION['email'];
    $fecha = date('Y/m/j H:i:s');
    $aciertos = (isset($_POST['aciertos'])) ? (int) $_POST['aciertos'] : 0;
    $nulas = (isset($_POST['nulas'])) ? (int) $_POST['nulas'] : 0;
    $fallos = (isset($_POST['fallos'])) ? (int) $_POST['fallos'] : 0;
    $nota = (isset($_POST['nota'])) ? (int) $_POST['nota'] : 0;
    $reloj = (isset($_POST['reloj'])) ? (int) $_POST['reloj'] : 0;

    $total = $aciertos + $nulas + $fallos;
    $errores = array();

    for ($i=1; $i<=$total ; $i++) {
        $solucion = (isset($_POST['solucion'.$i])) ? (string) $_POST['solucion'.$i] : '';
        $respuesta = (isset($_POST['respuesta'.$i])) ? (string) $_POST['respuesta'.$i] : '';
        $codigo = (isset($_POST['codigo'.$i])) ? (string) $_POST['codigo'.$i] : '';

        if (trim($solucion)==trim($respuesta)) {
            # SE HA ACERTADO ESA PREGUNTA, LUEGO LA QUITAMOS
            $errores[] = $codigo;
            $grabar = new ErrorCap();
            $grabar->borrarErrores($errores);
        }
    }
    //$app->redirect(INIT);
    $info = "Se ha realizado la grabación de sus datos. Puede consultarlo cuando desee en la zona Premium";
    $app->render('activar.phtml', array('info' => $info));
});
/**
 * TEST DE RESULTADOS
 */
$app->get('/cap/test/resultados(/)', $mwAcceso($app), function () use ($app) {
    $resultado = new Resultado();
    $app->render('resultado.phtml', array(
        'resultados' => $resultado->getResultado(),
        'enlace' => 'test > resultado'
    ));
});

/**
 * BUSQUEDA
 */
$app->get('/cap/test/buscar/(:tipo/:buscar(/))', $mwAcceso($app), function ($tipo, $buscar) use ($app) {
    $permitido = array(
        'pregunta',
        'codigo'
    );
    if (!in_array($tipo, $permitido)) {
        $app->notFound();
    }

    $healthy = array("á", "é", "í", "ó", "ú");
    $yummy   = array("a", "e", "i", "o", "u");

    $buscar = str_replace($healthy, $yummy, $buscar);

    if (preg_match('/^[áéíóúñ,;:?¿a-zA-Z0-9-]+$/', $buscar)) {
        // vuelvo a poner la cadena como corresponde - cambiar los - por espacios
        $buscar = str_replace("-", " ", $buscar);

        $pregunta = new Pregunta();
        $app->render('busqueda.phtml', array(
            'preguntas' => $pregunta->getPregunta($tipo, $buscar),
            'enlace' => 'test > busqueda > '.$tipo.' > '.$buscar
        ));
    } else {
        $app->notFound();
    }
});
/**
 * TEST DE VIAJEROS
 */
$app->get('/cap/test/viajero/(:enlace/:numero(/))', function ($enlace = 'todas', $numero = 10) use ($app) {
    $permitido = array(
        'todas',
        'conduccion-racional',
        'aplicacion-reglamento',
        'salud-seguridad-logistica'
    );

    if (!in_array($enlace, $permitido)) {
        $app->notFound();
    }

    $array = explode('-', $enlace);
    switch (count($array)) {
        case 0:
        case 1:
            $key = '';
            break;
        case 2:
            $key = $array[0].' '.$array[1];
            break;
        case 3:
            $key = $array[0].', '.$array[1].' y '.$array[2];
            break;
        default:
            $key = '';
    }
    /*
    if (count($array)>1) {
        $key = $array[0].' '.$array[1];
    } else {
        $key = '';
    }
    */
    $numeroPreguntas = (int) $numero;

    if ($numeroPreguntas < 10) {
        $app->notFound();
    } elseif ($numeroPreguntas > 100) {
        $app->notFound();
    }

    $enlace1 = "viajero-".$enlace;
    $pregunta = new Pregunta();
    $app->render('test.phtml', array(
        'preguntas' => $pregunta->getTest($enlace1, $numeroPreguntas, ''),
        'enlace' => 'test > viajero > '.$enlace,
        'title' => 'Test del CAP de viajeros '.$key,
        'descripcion' => 'Permite hacer test generales del CAP de viajeros '.$key.' con '.$numero. ' preguntas',
        'keyWords' => 'examenes CAP viajeros '.$key.', test CAP viajeros '.$key.', prueba CAP viajeros '.$key,
        'elTitulo' => 'Test del CAP de Viajeros ',
        'subTitulo' => $key
    ));
});

/**
 * TEST DE MERCANCIAS
 */
$app->get('/cap/test/mercancia/(:enlace/:numero(/))', function ($enlace = 'todas', $numero = 10) use ($app) {
    $permitido = array(
        'todas',
        'conduccion-racional',
        'aplicacion-reglamento',
        'salud-seguridad-logistica'
    );

    if (!in_array($enlace, $permitido)) {
        $app->notFound();
    }

    $array = explode('-', $enlace);
    switch (count($array)) {
        case 0:
        case 1:
            $key = '';
            break;
        case 2:
            $key = $array[0].' '.$array[1];
            break;
        case 3:
            $key = $array[0].', '.$array[1].' y '.$array[2];
            break;
        default:
            $key = '';
    }
    /*
    if (count($array)>1) {
        $key = $array[0].' '.$array[1];
    } else {
        $key = $enlace;
    }
    */
    $numeroPreguntas = (int) $numero;

    if ($numeroPreguntas < 10) {
        $app->notFound();
    } elseif ($numeroPreguntas > 100) {
        $app->notFound();
    }

    $enlace1 = 'mercancia-'.$enlace;
    $pregunta = new Pregunta();
    $app->render('test.phtml', array(
        'preguntas' => $pregunta->getTest($enlace1, $numeroPreguntas, ''),
        'enlace' => 'test > mercancía > '.$enlace,
        'title' => 'Test del CAP de mercancía '.$key,
        'descripcion' => 'Permite hacer test generales del CAP de mercancías '.$key.' con '.$numero. ' preguntas',
        'keyWords' => 'examenes CAP mercancías '.$key.', test CAP mercancías '.$key.', prueba CAP mercancías '.$key,
        'elTitulo' => 'Test del CAP de Mercancías',
        'subTitulo' => $key
    ));
});

/**
 * TEST DE COMUNES
 */
$app->get('/cap/test/comunes/(:enlace/:numero(/))', function ($enlace = 'todas', $numero = 10) use ($app) {
    $permitido = array(
        'todas',
        'conduccion-racional',
        'aplicacion-reglamento',
        'salud-seguridad-logistica'
    );

    if (!in_array($enlace, $permitido)) {
        $app->notFound();
    }

    $array = explode('-', $enlace);
    switch (count($array)) {
        case 0:
        case 1:
            $key = '';
            break;
        case 2:
            $key = $array[0].' '.$array[1];
            break;
        case 3:
            $key = $array[0].', '.$array[1].' y '.$array[2];
            break;
        default:
            $key = '';
    }
    /*
    if (count($array)>1) {
        $key = $array[0].' '.$array[1];
    } else {
        $key = $enlace;
    }
    */
    $numeroPreguntas = (int) $numero;

    if ($numeroPreguntas < 10) {
        $app->notFound();
    } elseif ($numeroPreguntas > 100) {
        $app->notFound();
    }

    $enlace1 = 'comunes-'.$enlace;
    $pregunta = new Pregunta();
    $app->render('test.phtml', array(
        'preguntas' => $pregunta->getTest($enlace1, $numeroPreguntas, ''),
        'enlace' => 'test > comunes > '.$enlace,
        'title' => 'Test del CAP comunes '.$key,
        'descripcion' => 'Permite hacer test generales del CAP comunes '.$key.' con '.$numero. ' preguntas',
        'keyWords' => 'examenes CAP comunes '.$key.', test CAP comunes '.$key.', prueba CAP comunes '.$key,
        'elTitulo' => 'Test del CAP comunes ',
        'subTitulo' => $key
    ));
});

/**
 * TEST DE SIMULACROS DE MERCANCIA
 */
$app->get('/cap/test/simulacro/mercancia(/)', function () use ($app) {

    $pregunta = new Pregunta();
    $app->render('test.phtml', array(
        'preguntas' => $pregunta->getSimulacro('mercancia', ''),
        'enlace' => 'test > simulacro > mercancias',
        'title' => 'Simulacro del CAP de mercancía ',
        'descripcion' => 'Simulacro de examen del CAP de mercancías, simulacro de test del CAP de mercancías', 'simulacro del CAP de mercancías',
        'keyWords' => 'simulacro examenes CAP mercancías, simulacro test CAP mercancías','simulacro CAP de mercancías',
        'elTitulo' => 'Simulacro del CAP de mercancías',
        'subTitulo' => ''
    ));
});

/**
 * TEST DE SIMULACRO DE VIAJEROS
 */
$app->get('/cap/test/simulacro/viajero(/)', function () use ($app) {

    $pregunta = new Pregunta();
    $app->render('test.phtml', array(
        'preguntas' => $pregunta->getSimulacro('viajero', ''),
        'enlace' => 'test > simulacro > viajeros',
        'title' => 'Simulacro del CAP de viajeros ',
        'descripcion' => 'Simulacro de examen del CAP de viajeros, simulacro de test del CAP de viajeros', 'simulacro del CAP de viajeros',
        'keyWords' => 'simulacro examenes CAP viajeros, simulacro test CAP viajeros','simulacro CAP de viajeros',
        'elTitulo' => 'Simulacro del CAP de viajeros',
        'subTitulo' => ''
    ));
});

/**
 * TEST EXAMENES REALES
 * 1º muestra todos los que hay, y el 2º te enseña el elegido
 *
 */
$app->get('/cap/test/examenes-reales(/)', $mwAcceso($app), function () use ($app) {

    $ruta = ROOT_DIR.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'test-reales';

    /**
     * listamos los archivos del directorio test-reales
     */
    $fichero = array();
    $testMercancias = array();
    $testViajeros = array();

    $directorio = opendir($ruta.DIRECTORY_SEPARATOR.'mercancia');
    while ($archivos = readdir($directorio))
    {
        $fichero[] = $archivos;
    }
    sort($fichero);
    foreach ($fichero as $ficheros) {
        if (!in_array($ficheros, array(".",".."))) {
            $testMercancias[] = str_replace('.txt','',$ficheros);
        }
    }

    $fichero = null;
    $fichero = array();
    $directorio = opendir($ruta.DIRECTORY_SEPARATOR.'viajero');
    while ($archivos = readdir($directorio))
    {
        $fichero[] = $archivos;
    }
    sort($fichero);
    foreach ($fichero as $ficheros) {
        if (!in_array($ficheros, array(".",".."))) {
            $testViajeros[] = str_replace('.txt','',$ficheros);
        }
    }

    $app->render('examenes-reales.phtml', array(
        'archivoMercancia' => $testMercancias,
        'archivoViajero' => $testViajeros,
        'enlace' => 'examenes reales'
    ));
});

$app->get('/cap/test/examenes-reales/:tipo/(:nombre(/))', $mwAcceso($app), function ($tipo, $nombre = '') use ($app) {
    $permitido = array(
        'mercancia',
        'viajero'
    );
    if (!in_array($tipo, $permitido)) {
        $app->notFound();
    }

    if (preg_match('/^[a-zA-Z0-9_-]+$/', $nombre)) {
        $nombre = trim(strtolower($nombre));

        $find = array('á', 'é', 'í', 'ó', 'ú', 'ñ');
        $repl = array('a', 'e', 'i', 'o', 'u', 'n');
        $nombre = str_replace ($find, $repl, $nombre);
        str_replace(' ', '-', $nombre);

        $pregunta = new Pregunta();
        $app->render('test.phtml', array(
            'preguntas' => $pregunta->getSimulacroExamenes($nombre, $tipo),
            'enlace' => 'test > examenes reales > '.$nombre,
            'title' => 'examen real '.$nombre.' de fomento del CAP de '.$tipo,
            'descripcion' => 'examen real '.$nombre.' fomento del CAP de '.$tipo.', test real '.$nombre.' de fomento del CAP de '.$tipo,
            'keyWords' => 'test real '.$nombre.' examenes CAP de '.$tipo.', examen real '.$nombre.' CAP viajeros de '.$tipo,
            'elTitulo' => 'Test del CAP',
            'subTitulo' => 'Simulacros de Examenes Reales'
        ));
    } else {
        $app->notFound();
    }
});

/**
 * TEST Específicos
 *
 */
$app->get('/cap/test/especifico/:tipo/:clave/(:numero(/))', $mwAcceso($app), function ($tipo, $clave, $numero=10) use ($app) {
    $clave = htmlspecialchars($clave);
    $clave = addslashes($clave);
    $permitido = array('mercancia', 'viajeros');
    if (!in_array($tipo, $permitido)) {
        $app->notFound();
    }
    if (strpos($clave, " ")) {
        $app->notFound();
    }
    if (!isset($numero)) {
        $app->notFound();
    }
    $numero = (int)$numero;
    if ($numero>100 or $numero<10) {
        $app->notFound();
    }
    if (preg_match('/^[a-zA-Z0-9]+$/', $clave)) {
        $pregunta = new Pregunta();
        $app->render('test.phtml', array(
            'preguntas' => $pregunta->getEspecificoExamen($tipo, $clave, $numero),
            'enlace' => 'test > especifico > '.$tipo.' > '.$clave,
            'title' => 'examen específico de '.$tipo.' de fomento del CAP con '.$clave,
            'descripcion' => 'examen específico de '.$tipo.' de fomento del CAP con '.$clave.', examen específico de '.$tipo.' de fomento del CAP con '.$clave,
            'keyWords' => 'test específico de '.$tipo.' de fomento del CAP con '.$clave.', examen específico de '.$tipo.' de fomento del CAP con '.$clave,
            'elTitulo' => 'Test Específico del CAP',
            'subTitulo' => $clave
        ));
    } else {
        $app->notFound();
    }
});

/**
 * TEST ENVIO DE DATOS PARA SU GRABACIÓN
 *
 */
$app->post('/cap/test/grabar-datos(/)', $mwAcceso($app), function () use ($app) {
    if (!isset($_POST)){
        $app->notFound();
    }
    $usuario = $_SESSION['email'];
    $fecha = date('Y/m/j H:i:s');
    $aciertos = (isset($_POST['aciertos'])) ? (int) $_POST['aciertos'] : 0;
    $nulas = (isset($_POST['nulas'])) ? (int) $_POST['nulas'] : 0;
    $fallos = (isset($_POST['fallos'])) ? (int) $_POST['fallos'] : 0;
    $nota = (isset($_POST['nota'])) ? (int) $_POST['nota'] : 0;
    $reloj = (isset($_POST['reloj'])) ? (int) $_POST['reloj'] : 0;

    $total = $aciertos + $nulas + $fallos;

    # PRIMERO GRABAMOS EN LA TABLA nota LOS DATOS RECOGIDOS
    $resultado = new stdClass;

    $resultado -> acierto = $aciertos;
    $resultado -> nula = $nulas;
    $resultado -> fallo = $fallos;
    $resultado -> nota = $nota;
    $resultado -> reloj = $reloj;
    $resultado -> fecha = $fecha;
    $resultado -> usuario = $usuario;

    $nota = new Nota();
    $nota->addNota($resultado);

    for ($i=1; $i<=$total ; $i++) {
        $solucion = (isset($_POST['solucion'.$i])) ? (string) $_POST['solucion'.$i] : '';
        $respuesta = (isset($_POST['respuesta'.$i])) ? (string) $_POST['respuesta'.$i] : '';
        $codigo = (isset($_POST['codigo'.$i])) ? (string) $_POST['codigo'.$i] : '';

        if (trim($solucion)!=trim($respuesta)) {
            # NO SE HA ACERTADO ESA PREGUNTA
            # GRABAMOS EN LA TABLA errores EL CODIGO de la pregunta errónea
            $errores = new stdClass;
            $errores -> usuario = $usuario;
            $errores -> codigo = $codigo;

            $grabar = new ErrorCap();
            $grabar->addError($errores);
        }
    }
    $info = "Se ha realizado la grabación de sus datos. Puede consultarlo cuando desee en la zona Premium";
    $app->render('activar.phtml', array('info' => $info));
});