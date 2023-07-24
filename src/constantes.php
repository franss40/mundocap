<?php
/**
 * Created by PhpStorm.
 * User: Usuario
 * Date: 26/07/2018
 * Time: 19:44
 */


# DEFINIMOS LA ENTRADA DE LA BASE DATOS
define("LOCAL", "localhost");
define("USUARIO", "root");
define("CLAVE", "");
define("BDD", "mundocap");
# VERSIÓN
define("VERSION", "09 de Marzo del 2017");
# DEFINIMOS EL MODO DE TRABAJO: develoment(defecto), production, test
define("MODO", "development");
# ZONA
date_default_timezone_set('Europe/Madrid');
# ROOT
define("INIT", "/slim/prueba");
# DEBUG
define("DEBUG", true);



/*
define("LOCAL", "localhost");
define("USUARIO", "mundocap_1");
define("CLAVE", "Franss69");
define("BDD", "mundocap_1");
# VERSIÓN
define("VERSION", "09 de Marzo del 2017");
# DEFINIMOS EL MODO DE TRABAJO: develoment(defecto), production, test
define("MODO", "production");
# ZONA
date_default_timezone_set('Europe/Madrid');
# ROOT
define("INIT", "");
# DEBUG
define("DEBUG", false);
*/


/*
define("LOCAL", "sql204.byethost6.com");
define("USUARIO","b6_22794624");
define("CLAVE","asusR510K");
define("BDD","b6_22794624_mundocap");
define("VERSION", "09 de Marzo del 2017");
define("MODO", "production");
date_default_timezone_set('Europe/Madrid');
define("INIT", "");
define("DEBUG", false);
*/