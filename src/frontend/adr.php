<?php
/**
 * Created by PhpStorm.
 * User: Fran
 * Date: 02/12/2018
 * Time: 11:05
 */

$app->get('/adr(/)', function () use ($app) {
    $app->render('/adr/adr.phtml', array(
        'enlace' => 'ADR',
        'title' => '',
        'descripcion' => '',
        'keyWords' => ''
    ));
});