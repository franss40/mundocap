<?php
/**
 * Created by PhpStorm.
 * User: Fran
 * Date: 29/08/2018
 * Time: 11:05
 */

/**
 * LOGIN
 */
$app->get('/cap/login(/)', function () use ($app) {
    if (!empty($_SESSION['usuario'])){
        $info = "Ya has iniciado sesión.";
        $app->render('activar.phtml', array('info' => $info));
        exit;
    }

    //aquí vamos a borrar las ips antíguas de más de 24h.
    $person = new Login();
    $person->clearIP();
    $_SESSION['skey'] = md5(uniqid(mt_rand(), true));
    $app->render('login.phtml', array('skey' => $_SESSION['skey']));
});

$app->post('/cap/login(/)', function () use ($app) {
    if (!isset($_POST)){
        $app->notFound();
    }

    $usuario = (isset($_POST['email'])) ? (string) $_POST['email'] : '';
    $password = (isset($_POST['contrasena'])) ? (string) $_POST['contrasena'] : '';
    $skey = (isset($_POST['skey'])) ? (string) $_POST['skey'] : '';

    $info = '';
    $error = false;

    if (empty($_SESSION['skey'])){
        $info = 'Usuario o Password no son correctos';
        $error = true;
    }

    if (!$usuario || !$password) {$info = 'Usuario o Password no son correctos'; $error = true;}
    if ($skey!=$_SESSION['skey']) {$info = 'Usuario o Password no son correctos'; $error = true;};

    if (!preg_match('/^[a-zA-Z0-9_@.-]+$/', $usuario)) {
        $info = 'El E-mail no es correcto';
        $error = true;
    }
    if (!preg_match('/^[a-zA-Z0-9_@.-]+|(\*)+|(\+)+$/', $password)) {
        $info = 'El Password no es correcto';
        $error = true;
    }

    if (strlen($usuario)>40 || strlen($password)>40){
        $info = 'E-mail o Password exceden el límite permitido';
        $error = true;
    }

    $noUsar = array("REM ", "rem ", "/*", "--", "__");
    foreach ($noUsar as $item) {
        if (strpos($usuario, $item)!==false || strpos($password, $item)!==false) {
            $info = 'E-mail o Password contienen carácteres no permitidos';
            $error = true;
        }
    }

    if (!filter_var($usuario, FILTER_VALIDATE_EMAIL)){
        $info = 'E-mail o Password no son correctos';
        $error = true;
    }

    if ($error) {
        $app->render('login.phtml', array('info' => $info, 'skey' => $_SESSION['skey']));
    } else {
        $person = new Login();
        if ($person->comprobarAcceso($usuario, $password) && $_SERVER['HTTP_USER_AGENT']==$_SESSION['navegador']) {
            $_SESSION['usuario'] = 1;
            $_SESSION['email'] = $usuario;
            $app->render('activar.phtml', array('info' => 'Ya puede disfrutar de su contenido'));
            //$app->redirect(INIT);
        } else {
            $info = 'E-mail o Password no son correctos <br>';
            $info .= 'Si está baneado por exceso de intentos, tendrá que esperar 24 h. para poder acceder; o bién, puede contactar con el administrador vía e-mail';
            $app->render('login.phtml', array('info' => $info, 'skey' => $_SESSION['skey']));
        }
    }
});

/**
 * LOGOUT
 */
$app->get('/cap/logout(/)', function () use ($app) {
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    session_unset();
    $info = "Ha cerrado correctamente su sesión";
    $app->render('activar.phtml', array('info' => $info));
});

/**
 * ALTA
 */
$app->get('/cap/alta(/)', function () use ($app) {
    $_SESSION['skey'] = md5(uniqid(mt_rand(), true));
    $info = '';
    $app->render('alta.phtml', array('info' => $info, 'skey' => $_SESSION['skey']));
});
$app->post('/cap/alta(/)', function () use ($app) {
    if (!isset($_POST)){
        $app->notFound();
    }
    $usuario = (isset($_POST['email'])) ? (string) $_POST['email'] : '';
    $password = (isset($_POST['contrasena'])) ? (string) $_POST['contrasena'] : '';
    $skey = (isset($_POST['skey'])) ? (string) $_POST['skey'] : '';
    $info = '';
    $error = false;

    if (empty($_SESSION['skey'])){
        $info = 'Usuario o Password no son correctos';
        $_SESSION['skey'] = 0;
        $error = true;
    }
    if (!$usuario || !$password) {$info = 'Usuario o Password no son correctos'; $error = true;}
    if ($skey!=$_SESSION['skey']) {$info = 'Usuario o Password no son correctos'; $error = true;};
    if (strlen($usuario)>40 || strlen($password)>40){
        $info = 'E-mail o Password exceden el límite permitido';
        $error = true;
    }
    if (!preg_match('/^[a-zA-Z0-9_@.-]+$/', $usuario)) {
        $info = 'E-mail o Password no son correctos';
        $error = true;
    }
    if (!preg_match('/^[a-zA-Z0-9_@.-]+|(\*)+|(\+)+$/', $password)) {
        $info = 'E-mail o Password no son correctos';
        $error = true;
    }
    $noUsar = array("REM ", "rem ", "/*", "--", "__");
    foreach ($noUsar as $item) {
        if (strpos($usuario, $item)!==false || strpos($password, $item)!==false) {
            $info = 'E-mail o Password contienen carácteres no permitidos';
            $error = true;
        }
    }
    if (!filter_var($usuario, FILTER_VALIDATE_EMAIL)){
        $info = 'E-mail o Password no son correctos';
        $error = true;
    }
    if (!empty($_SESSION['usuario'])){
        $info = 'Usted tiene abierta una sesión actualmente; ya está dado de alta';
        $error = true;
    }
    $person = new Login();
    if ($person->comprobarUsuario($usuario)){
        $info = 'Usted ya está dado de alta';
        $error = true;
    }
    if ($error) {
        $app->render('alta.phtml', array('info' => $info, 'skey' => $_SESSION['skey']));
    } else {
        # hay que grabar antes en la bdd el email, contraseña y el aleatorio para el alta
        $aleatorio = md5(uniqid(mt_rand(), true));
        $access = new Login();
        $idGrabado = $access->altaEmail($usuario, $password, $aleatorio);

        $cabeceras = 'MIME-Version: 1.0' . "\r\n";
        $cabeceras .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $cabeceras .= 'From: mundoCap<info@mundocap.es>';
        $mje = "<h3>Bienvenido a <b>mundoCAP</b></h3>";
        $mje .= "<p>Usted recibe este correo por su intensión de darse de alta en este sitio.</p>";
        $mje .= "<p>Si no fuese así, ignore este mensaje</p>";

        $mje.= "<p>Para activar su registro por favor pulse en el siguiente enlace o bien pongaló en su navegador</p> ";
        $mje.= "<p><a href='www.mundocap.es/activar/$idGrabado/$aleatorio'>www.mundocap.es/activar/$idGrabado/$aleatorio</a></p>";
        $mje.= "<p>Recordarle que puede darse de baja cuando lo desee siguiendo el enlace siguiente: <a href='http://mundocap.es/cap/baja'>http://mundocap.es/cap/baja</a></p>";
        $mje.= "<p>Para cancelar su suscripción es necesario previamente iniciar sesión.</p>";
        $mje.= "<p>Espero que sea beneficiosa su estancia en mundoCAP</p>";
        $mje .= "<p>Gracias por registrarse</p>";

        if (mail($usuario, 'Bienvenido a mundoCAP!', $mje, $cabeceras)) {
            $info = "Se le acaba de envíar un email a su cuenta de correo para que pueda activar su cuenta. ";
            $info .= "Si no lo ve en la bandeja de entrada por favor compruebe en la de Spam. ";
            $info .= "Gracias por registrarse.";
            $app->render('activar.phtml', array('info' => $info));
        } else {
            $info = "Se ha producido un error. Por favor, vuelva a intentarlo pasados unos minutos.";
            $app->render('alta.phtml', array('info' => $info, 'skey' => $key));
        }

    }
});
$app->get('/activar/:id/:key(/)', function ($id, $key) use ($app) {
    $id = (int)$id;
    $key = (int)$key;
    $activar = new Login();
    if ($activar->comprobarActivacion($id, $key)){
        $info = "Gracias por activar su cuenta. Ya puede iniciar sesión y disfrutar de su contenido.";
        $app->render('activar.phtml', array('info' => $info));
    } else {
        $info = "Su activación no es correcta. Por favor póngase en contacto con el administrador";
        $app->render('activar.phtml', array('info' => $info));
    };
});
/**
 * RECUPERAR CONTRASEÑA
 */
$app->get('/cap/recuperar(/)', function () use ($app) {
    if (!empty($_SESSION['usuario'])){
        $info = "Ya has iniciado sesión.";
        $app->render('activar.phtml', array('info' => $info));
        exit;
    }
    $_SESSION['skey'] = md5(uniqid(mt_rand(), true));
    $app->render('recuperar.phtml', array('skey' => $_SESSION['skey']));
});
$app->post('/cap/recuperar(/)', function () use ($app) {
    if (!isset($_POST)){
        $app->notFound();
    }
    $usuario = (isset($_POST['email'])) ? (string) $_POST['email'] : '';
    $password = (isset($_POST['contrasena'])) ? (string) $_POST['contrasena'] : '';
    $skey = (isset($_POST['skey'])) ? (string) $_POST['skey'] : '';
    $info = '';
    $error = false;

    if (empty($_SESSION['skey'])){
        $info = 'Usuario o Password no son correctos';
        $_SESSION['skey'] = 0;
        $error = true;
    }
    if (!$usuario || !$password) {$info = 'Usuario o Password no son correctos'; $error = true;}
    if ($skey!=$_SESSION['skey']) {$info = 'Usuario o Password no son correctos'; $error = true;};
    if (strlen($usuario)>40 || strlen($password)>40){
        $info = 'E-mail o Password exceden el límite permitido';
        $error = true;
    }
    if (!preg_match('/^[a-zA-Z0-9_@.-]+$/', $usuario)) {
        $info = 'E-mail o Password no son correctos';
        $error = true;
    }
    if (!preg_match('/^[a-zA-Z0-9_@.-]+|(\*)+|(\+)+$/', $password)) {
        $info = 'E-mail o Password no son correctos';
        $error = true;
    }
    $noUsar = array("REM ", "rem ", "/*", "--", "__");
    foreach ($noUsar as $item) {
        if (strpos($usuario, $item)!==false || strpos($password, $item)!==false) {
            $info = 'E-mail o Password contienen carácteres no permitidos';
            $error = true;
        }
    }
    if (!filter_var($usuario, FILTER_VALIDATE_EMAIL))
    {
        $info = 'E-mail o Password no son correctos';
        $error = true;
    }
    if (!empty($_SESSION['usuario'])){
        $info = 'Usted tiene abierta una sesión actualmente, por lo que ya está dado de alta';
        $error = true;
    }
    $person = new Login();
    if (!$person->comprobarUsuario($usuario)){
        $info = 'Este email no está dado de alta';
        $error = true;
    }

    if ($error) {
        $app->render('recuperar.phtml', array('info' => $info, 'skey' => $skey));
    } else {
        # hay que grabar antes en la bdd el email, contraseña y el aleatorio para el alta
        $access = new Login();
        $access->borrarRegistro($usuario);
        $aleatorio = md5(uniqid(mt_rand(), true));
        $idGrabado = $access->altaEmail($usuario, $password, $aleatorio);

        $cabeceras = 'MIME-Version: 1.0' . "\r\n";
        $cabeceras .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $cabeceras .= 'From: MundoCap<info@mundocap.es>';
        $mje = "<h3>Bienvenido a <b>mundoCAP</b></h3>";
        $mje .= "<p>Usted recibe este correo por su intensión de recuperar su acceso a este sitio.</p>";
        $mje .= "<p>Si no fuese así, ignore este mensaje</p>";

        $mje.= "<p>Para recuperar su registro por favor pulse en el siguiente enlace o bien pongaló en su navegador</p> ";
        $mje.= "<p><a href='www.mundocap.es/activar/$idGrabado/$aleatorio'>www.mundocap.es/activar/$idGrabado/$aleatorio</a></p>";
        $mje.= "<p>Espero que sea beneficiosa su estancia en mundoCAP</p>";
        $mje .= "<p>Gracias por confiar en esta Web</p>";

        if (mail($usuario, 'Bienvenido de nuevo a mundoCAP!', $mje, $cabeceras)) {
            $info = "Por favor confirme en su correo el acceso. Buén aprendizaje.";
            $app->render('activar.phtml', array('info' => $info));
            //$app->render('recuperar.phtml', array('info' => $info, 'skey' => $_SESSION['skey']));
        } else {
            $info = "Se ha producido un error. Por favor, vuelva a intentarlo pasados unos minutos.";
            $app->render('recuperar.phtml', array('info' => $info, 'skey' => $skey));
        }
    }
});
/**
 * BAJA
 */
$app->get('/cap/baja(/)', function () use ($app) {
    $_SESSION['skey'] = md5(uniqid(mt_rand(), true));
    $info = '';
    $app->render('baja.phtml', array('info' => $info, 'skey' => $_SESSION['skey']));
});

$app->post('/cap/baja(/)', function () use ($app) {
    if (!isset($_POST) || !isset($_SESSION['usuario'])){
        $info = "Debe de estar dado de alta para poder usar la opción de baja.";
        $app->render('activar.phtml', array('info' => $info));
        exit;
    }
    $skey = (isset($_POST['skey'])) ? (string) $_POST['skey'] : '';
    $usuario = (isset($_POST['email'])) ? (string) $_POST['email'] : '';

    if ($skey!=$_SESSION['skey']) {
        $info = 'Usuario o Password no son correctos';
        $app->render('activar.phtml', array('info' => $info));
        exit;
    }

    # hay que grabar antes en la bdd el el aleatorio para que pueda aceptar la baja.
    if (empty($_SESSION['usuario'])){
        $info = "Debe de estar dado de alta para poder usar la opción de baja.";
        $app->render('activar.phtml', array('info' => $info));
        exit;
    }
    if ($usuario!=$_SESSION['email']) {
        $info = "No es un correo válido";
        $app->render('activar.phtml', array('info' => $info));
        exit;
    }

    $access = new Login();
    $aleatorio = md5(uniqid(mt_rand(), true));
    $id = $access->actualizarRnd($aleatorio);

    $cabeceras = 'MIME-Version: 1.0' . "\r\n";
    $cabeceras .= 'Content-type: text/html; charset=utf-8' . "\r\n";
    $cabeceras .= 'From: MundoCap<info@mundocap.es>';
    $mje = "<h3><b>mundoCAP</b></h3>";
    $mje .= "<p>Usted recibe este correo por su intensión de darse de baja </p>";
    $mje .= "<p>Si no fuese así, ignore este mensaje</p>";

    $mje.= "<p>Para dar de baja su registro por favor pulse en el siguiente enlace o bien pongaló en su navegador</p> ";
    $mje.= "<p><a href='www.mundocap.es/borrar/$id/$aleatorio'>www.mundocap.es/borrar/$id/$aleatorio</a></p>";
    $mje.= "<p>Espero que haya sido beneficiosa su estancia en mundoCAP</p>";
    $mje .= "<p>Gracias por haber confiado en esta Web</p>";

    if (mail($_SESSION['email'], 'mundoCAP!', $mje, $cabeceras)) {
        $info = "Por favor confirme en su correo la baja";
        $app->render('activar.phtml', array('info' => $info));
        //$app->render('recuperar.phtml', array('info' => $info, 'skey' => $_SESSION['skey']));
    } else {
        $info = "Se ha producido un error. Por favor, vuelva a intentarlo pasados unos minutos.";
        $app->render('activar.phtml', array('info' => $info, 'skey' => $key));
    }
});

$app->get('/borrar/:id/:key(/)', function ($id, $key) use ($app) {
    $usuario = new Login();
    if ($usuario->bajaDefinitiva($id, $key)){
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        session_unset();
        $info = "Se ha dado usted de baja satisfactoriamente. Esperamos verle de nuevo pronto por aquí.";
        $app->render('activar.phtml', array('info' => $info, 'skey' => $key));
    } else {
        $info = "Se ha producido un error. Por favor, vuelva a intentarlo pasados unos minutos.";
        $app->render('activar.phtml', array('info' => $info, 'skey' => $key));
    }
});

/**
 * EMAIL
 */
$app->get('/cap/email(/)', function () use ($app) {
    $_SESSION['skey'] = md5(uniqid(mt_rand(), true));
    $aleatorio1 = mt_rand(0, 9);
    $aleatorio2 = mt_rand(0, 9);
    $app->render('email.phtml', array(
        'skey' => $_SESSION['skey'],
        'numero1' => $aleatorio1,
        'numero2' => $aleatorio2
    ));
});

$app->post('/cap/email(/)', function () use ($app) {
    $nombre = (isset($_POST['nombre'])) ? (string)$_POST['nombre'] : '';
    $usuario = (isset($_POST['email'])) ? (string)$_POST['email'] : '';
    $texto = (isset($_POST['texto'])) ? (string)$_POST['texto'] : '';
    $resultado = (isset($_POST['multiplicar'])) ? (string)$_POST['multiplicar'] : '';
    $skey = (isset($_POST['skey'])) ? (string)$_POST['skey'] : '';
    $numero1 = (isset($_POST['numero1'])) ? (int)$_POST['numero1'] : 11;
    $numero2 = (isset($_POST['numero2'])) ? (int)$_POST['numero2'] : 11;

    $error = '';
    if (!$skey || $nombre != '' || $skey != $_SESSION['skey'] || $numero1>9 || $numero2>9) {
        $app->notFound();
    }

    if ($resultado!=$numero1*$numero2) {
        $info = 'Hay datos que no son correctos';
        $error = true;
    }

    if (!$usuario || !$texto) {
        $info = 'Debe de rellenar los datos solicitados';
        $error = true;
    }

    if (!preg_match('/^[a-zA-Z0-9_@.-]+$/', $usuario)) {
        $info = 'El e-mail no es correcto';
        $error = true;
    }

    if (!filter_var($usuario, FILTER_VALIDATE_EMAIL)) {
        $info = 'E-mail no es correcto';
        $error = true;
    }

    if (strlen($usuario) > 40) {
        $info = 'E-mail exceden el límite permitido';
        $error = true;
    }

    $noUsar = array("REM ", "rem ", "/*", "--", "__", "//");
    foreach ($noUsar as $item) {
        if (strpos($usuario, $item) !== false || strpos($texto, $item) !== false) {
            $info = 'E-mail o Texto contienen carácteres no permitidos';
            $error = true;
        }
    }

    if ($error) {
        $aleatorio1 = mt_rand(0, 9);
        $aleatorio2 = mt_rand(0, 9);
        $app->render('email.phtml', array(
            'info' => $info,
            'skey' => $_SESSION['skey'],
            'numero1' => $aleatorio1,
            'numero2' => $aleatorio2
        ));
    } else {
        $texto .= "\n\n\n De: ".$usuario;
        if (mail('franss40@gmail.com, info@mundocap.es', 'mundoCAP!', $texto, 'From: info@mundocap.es')) {
            $info = "Recibirá una respuesta lo más pronto posible. Gracias por confiar en mundoCAP.";
            $app->render('activar.phtml', array('info' => $info));
            //$app->render('recuperar.phtml', array('info' => $info, 'skey' => $_SESSION['skey']));
        } else {
            $info = "Se ha producido un error. Por favor, vuelva a intentarlo pasados unos minutos.";
            $app->render('activar.phtml', array('info' => $info, 'skey' => $key));
        }
    }
});
/**
 * COOKIES Y POLITICA
 */
$app->get('/cap/cookies(/)', function () use ($app) {
    $app->render('cookies.phtml');
});

/**
 * HACE QUE NO SE MUESTRE MÁS EL AVISO DE LAS COOKIES
 */
$app->post('/remover(/)', function () use ($app) {
    $_SESSION['ocultarC'] = true;
});