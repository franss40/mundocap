<?php
/*
*    ruta de la home ( index )
 */

//class Usuario extends Model {}
//Aquí añado la ruta automático del modelo .\src\models\
spl_autoload_register(function ($nombre_clase) {
    //
    $mipath = ROOT_DIR.DIRECTORY_SEPARATOR.'src'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR;
    $mifila = $mipath.DIRECTORY_SEPARATOR.$nombre_clase.'.php';
    if (file_exists($mifila)) {
        include_once $mifila;
    }
});

$app->get('(/)', function () use ($app) {
    $app->render('inicio.phtml');
});

/*
*   Los paréntesis significan que el interior es opcional.
*   Los dos puntos,:, antes de lang, le convierte en una variable
*/
/*
$app->get('/(:lang(/))', function ($lang = 'es') use($app) {
    $permitido = array("es", "en");
    if (!in_array($lang, $permitido)) {
        $app->notFound();
    }
    $app->render('inicio.phtml');
})->name('home.index');
*/