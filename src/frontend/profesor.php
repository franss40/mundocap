<?php
/*****
    Esta pendiente de resolución, borrar y actualizarlo correctamente
 ***********/
$app->post('/cap/test/crear-examen(/)', $mwAcceso($app), function () use ($app) {
    if (!isset($_POST)){
        $app->notFound();
    }
    $person = new Login();
    $cod = $person->retornarCod();

    $aciertos = (isset($_POST['aciertos'])) ? (int) $_POST['aciertos'] : 0;
    $nulas = (isset($_POST['nulas'])) ? (int) $_POST['nulas'] : 0;
    $fallos = (isset($_POST['fallos'])) ? (int) $_POST['fallos'] : 0;
    $total = $aciertos + $nulas + $fallos;

    for ($i=1; $i<=$total ; $i++) {
        $codigo = (isset($_POST['codigo'.$i])) ? (string) $_POST['codigo'.$i] : '';

        $profesor = new Profesor();
        $profesor->grabarExamen($cod, $codigo);
    }
    exit;
    $info = "El código para compartir con sus alumnos es el ".$cod.". Para más información puede ir
    al enlace de administración del profesor";
    $app->render('crear-examen.phtml', array('info' => $info));
});