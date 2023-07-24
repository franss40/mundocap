<?php

$app->get('/email(/)', function () use ($app) {
    $app->redirect('/cap/email', 301);
});

$app->get('/cookies(/)', function () use ($app) {
    $app->redirect('/cap/cookies', 301);
});

$app->get('/servidor/:name(/)', function ($name) use ($app) {
    switch ($name) {
        case 'index.php':
            $app->redirect(INIT.'/', 301);
            break;
        case 'aviso2.php':
            $app->redirect(INIT.'/cap/cookies', 301);
            break;
        case 'busqueda.php':
            $app->redirect(INIT.'/', 301);
            break;
        case 'inicioSesion.php':
            $app->redirect(INIT.'/cap/login', 301);
            break;
        case 'alta.php':
            $app->redirect(INIT.'/cap/alta', 301);
            break;
        case 'contacta.php':
            $app->redirect(INIT.'/cap/email', 301);
            break;
        case 'test-cap.php':
            $app->redirect(INIT.'/', 301);
            break;
        case 'test-personalizado.php':
            $app->redirect(INIT.'/', 301);
            break;
        case 'aprende2.php':
            $app->redirect(INIT.'/', 301);
            break;
        default:
            $app->notFound();
    }
});
