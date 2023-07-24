<?php
/**
 * Created by PhpStorm.
 * User: Usuario
 * Date: 14/10/2018
 * Time: 15:23
 *
 * TEST REALES CREACIÓN
 * Hay que ir hacia esta ruta una vez que se haya grabado en examen.txt los datos obtenidos
 * de la web www.juntadeandalucia.es/organismos/fomentoyvivienda/areas/servicios-transporte/servicios-transportista/paginas/gestion-formacion-cap-cadiz2018.html
 * Una vez hecho eso se crea dentro de la carpeta mercancias o viajeros el archivo txt correspondiente
 * Es ahí dónde pondremos las preguntas que nos salen en /cap/crear
 */
$app->get('/cap/crear(/)', $mwAcceso($app), function () use ($app) {
    try{
        $z = new Pregunta();
        //abrimos el archivo correspondiente
        $handle = fopen("examen.txt", "r") or die("No se pudo abrir el archivo");
        $inicio = true;
        $linea = '';

        while (!feof($handle)) {
            $numero = $cod = $pregunta = $a = $b = $c = $d = $sol = '';

            if ($inicio) {
                $numero = trim(fgets($handle));   //la primera o siguiente pertenece al código de la pregunta
                $inicio = false;
            } else {
                $numero = $linea;
            }

            fgets($handle); // La siguiente línea se omite

            $contador = 0;
            # Algoritmo para la pregunta
            while ($contador==0) {
                $linea = trim(fgets($handle));
                if (strlen($linea)>1) {
                    $pregunta .= $linea.' ';
                } else {
                    $pregunta = trim($pregunta);
                    if ($linea == '*') {
                        $sol = 'A';
                        fgets($handle); // La siguiente línea se omite
                    }
                    fgets($handle); // La siguiente línea se omite
                    break;
                }
            }

            # Algoritmo para la respuesta a
            while ($contador==0) {
                $linea = trim(fgets($handle));
                if (strlen($linea)>1) {
                    $a .= $linea.' ';
                } else {
                    $a = trim($a);
                    if ($linea == '*') {
                        $sol = 'B';
                        fgets($handle); // La siguiente línea se omite
                    }
                    fgets($handle); // La siguiente línea se omite
                    break;
                }
            }

            # Algoritmo para la respuesta b
            while ($contador==0) {
                $linea = trim(fgets($handle));
                if (strlen($linea)>1) {
                    $b .= $linea.' ';
                } else {
                    $b = trim($b);
                    if ($linea == '*') {
                        $sol = 'C';
                        fgets($handle); // La siguiente línea se omite
                    }
                    fgets($handle); // La siguiente línea se omite
                    break;
                }
            }
            # Algoritmo para la respuesta c
            while ($contador==0) {
                $linea = trim(fgets($handle));
                if (strlen($linea)>1) {
                    $c .= $linea.' ';
                } else {
                    $c = trim($c);
                    if ($linea == '*') {
                        $sol = 'D';
                        fgets($handle); // La siguiente línea se omite
                    }
                    fgets($handle); // La siguiente línea se omite
                    break;
                }
            }
            # Algoritmo para la respuesta d
            while ($contador==0) {
                $linea = trim(fgets($handle));
                if (is_numeric($linea)){
                    break;
                }
                if (substr($linea,0,4)=='refe') {
                    fgets($handle);
                    fgets($handle);
                    $linea = '';
                    continue;
                }
                if (strlen($linea)>1) {
                    $d .= $linea.' ';
                } else {
                    $d = trim($d);
                    break;
                }
            }

            /****************************************************
            // cálculo de los códigos
             * ****************************************************/

            $healthy = array("á", "é", "í", "ó", "ú", "  ", "...");
            $yummy   = array("a", "e", "i", "o", "u", " ", ".");
            $preguntaC = str_replace($healthy, $yummy, $pregunta);

            $posI = strpos($preguntaC, 'ñ');

            if ($posI>5) {
                $pregunta3 = substr($preguntaC, 1, $posI-1);
            } else {
                $pregunta3 = substr($preguntaC, 1, strlen($preguntaC)-3);
            }

            $pregunta1 = substr($preguntaC, 1, (strlen($preguntaC)/2));
            $pregunta2 = substr($preguntaC, -(strlen($preguntaC)/2), strlen($preguntaC)/2-1);

            $elarray = array();
            echo 'codigos: ';
            $respuestas = $z->getCodigo($pregunta3);
            $contar = 0;
            foreach ($respuestas as $salida) {
                $contar++;
                if (trim($salida->solucion)==$sol) {
                    echo $salida->cod.'<br>';
                    $elarray[] = trim($salida->cod);
                }
            }

            if ($contar==0 || $contar>1) {
                $posI = strpos($preguntaC, '¿');
                $posF = strpos($preguntaC, '?');

                $contar2 = 0;
                if ($posI!==false) {
                    $pregunta3 = substr($pregunta3, $posI+1, $posF-2);
                    $respuestas = $z->getCodigo($pregunta3);
                    echo '<br>--------------'.'<br>';
                    foreach ($respuestas as $salida) {
                        if (trim($salida->solucion)==$sol) {
                            if (count($elarray)>0 && in_array(trim($salida->cod), $elarray)) {
                                //nada
                            } else {
                                echo $salida->cod.'<br>';
                                $contar2++;
                            }
                        }
                    }
                }

                if ($contar2==0 || $contar2>1) {
                    echo '-----';
                    $respuestas = $z->getCodigo($pregunta2);
                    $contar2 = 0;
                    echo '<br>--------------'.'<br>';
                    foreach ($respuestas as $salida) {
                        if (trim($salida->respA)==$a && trim($salida->respB)==$b) {
                            echo 'nuevo: '.$salida->cod . '<br>';
                        }
                    }

                    $respuestas = $z->getCodigo($pregunta1);
                    echo '<br>--------------'.'<br>';
                    foreach ($respuestas as $salida) {
                        if (trim($salida->respA)==$a && trim($salida->respB)==$b) {
                            echo 'nuevo: '.$salida->cod . '<br>';
                        }
                    }
                }
            }

            echo '<br><br><br>';
            /************************************************************
             * fin de cálculo de códigos
             ***********************************************************/

            echo $numero.' <h3>'.$pregunta.'</h3><br>';
            echo 'a. '.$a.'<br>';
            echo 'b. '.$b.'<br>';
            echo 'c. '.$c.'<br>';
            echo 'd. '.$d.'<br>';
            echo 'sol: <strong>'.$sol.'</strong><br>';
            echo '<br><br><br>';
        }
    } catch (Exception $e) {
        echo $e; //$app->notFound();
    }
});
/**
 * Created by PhpStorm.
 * User: Fran
 * Date: 25/11/2018
 *
 * CREACIÓN PREGUNTAS PARA EL ADR
 * Esto añade las preguntas que esten en el formato adecuado a la base de datos
 * Comprueba que no esté repetida antes.
 * El formato es pregunta Intro respA Intro *respB Intro respC Intro foto Intro + Intro
 * El * indica la pregunta correcta.
 */
$app->get('/adr/add(/)', $mwAcceso($app), function () use ($app) {
    $test = new PreguntaADR();

    try {
        //abrimos el archivo correspondiente
        $handle = fopen("ADR.txt", "r") or die("No se pudo abrir el archivo");

        while (!feof($handle)) {
            $sol = '';
            $pregunta = '';
            $respA = '';
            $respB = '';
            $respC = '';
            $foto = '0';

            $pregunta = trim(fgets($handle));
            $respA = trim(fgets($handle));
            if (substr($respA, 0, 1) == '*') {
                $sol = 'A';
                $respA = substr($respA, 1);
            }
            $respB = fgets($handle);
            if (substr($respB, 0, 1) == '*') {
                $sol = 'B';
                $respB = substr($respB, 1);
            }
            $respC = fgets($handle);
            if (substr($respC, 0, 1) == '*') {
                $sol = 'C';
                $respC = substr($respC, 1);
            }
            $foto = fgets($handle);

            echo $pregunta.'<br>';
            echo $respA.'<br>';
            echo $respB.'<br>';
            echo $respC.'<br>';
            echo $sol.'<br>';
            echo $foto.'<br><hr>';
            $aa = $test->isAdd($pregunta);
            if ($aa or $sol=='') {
                echo '<strong style="color: red;">No se puede añadir la pregunta</strong><br>'.$pregunta;
                echo '<br>';
            } else {
                $test->addTest($pregunta, $respA, $respB, $respC, $sol, 'basico');
            }
            fgets($handle);
        }



        fclose($handle);
    } catch (Exception $e) {
        echo $e;
    }
});