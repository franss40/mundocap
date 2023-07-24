$(document).foundation()

$(".nuevo").hide();
var altura = $("#parallax-1").offset().top;
$(window).scroll(function(){
    if ($(window).scrollTop()>altura-500) {
        $(".nuevo").fadeIn(1100);
    }
});

$('h1').animate({marginLeft:'0px'},1000,'swing');

// elimina información de las cookies
function remover(){
    const a = $('#ojo').attr('data-order');

    $('.ocultar').hide('slow');
    $.ajax({
        /* data: {"parametro1" : valor1, "parametro2" : valor2},
            datatype: "json", */
        type : "POST",
        url : $(this).attr('data-order'),
        success:function(response){
            $('#cookies').html(response);
        }
    })
}

$(document).ready(function() {

    $(".remove").on("click", remover);
    /*remover la info de cookies*/

    $(".remove2").click(function(){
        $.ajax({
            /* data: {"parametro1" : valor1, "parametro2" : valor2},
                datatype: "json", */
            type : "POST",
            url : $(this).attr('data-order'),
            success:function(response){
                $('#cookies').html(response);
            }
        })
    });

    /****************************************************************************
     * Rutas de Específicos
     ****************************************************************************/
    // Pasamos la ruta despues de introducir los datos de test específico por clave(id=clave)
    // y por número de preguntas
    $('#beginEspecifico').bind('click', function (event) {
        var $tipo = $('#tipoEspecifico').val();
        var $clave = $('#clave').val();
        var $preguntas = $('#preguntas').val();

        var expreg = /[^áéíóú0-9A-Za-z_]/;

        if (($preguntas>=10) && ($preguntas<=100) && (!expreg.test($clave))) {
            $(location).attr('href', './cap/test/especifico/' + $tipo + '/' + $clave + '/' + $preguntas);
        } else {
            $(".E").html("Valores no válidos");
        }
        event.preventDefault();
    });
    /****************************************************************************
     * Rutas de Mercancias
     ****************************************************************************/
    $('#beginMercanciaMT').bind('click', function (event) {
        var $preguntas = $('.preguntasMT').val();
        if (($preguntas>=10) && ($preguntas<=100)) {
            $(location).attr('href', './cap/test/mercancia/todas/' + $preguntas);
        } else {
            $(".MT").html("Valores no válidos");
        }
        event.preventDefault();
    });

    $('#beginMercanciaMC').bind('click', function (event) {
        var $preguntas = $('.preguntasMC').val();
        if (($preguntas>=10) && ($preguntas<=100)) {
            $(location).attr('href', './cap/test/mercancia/conduccion-racional/' + $preguntas);
        } else {
            $(".MC").html("Valores no válidos");
        }
        event.preventDefault();
    });

    $('#beginMercanciaMA').bind('click', function (event) {
        var $preguntas = $('.preguntasMA').val();
        if (($preguntas>=10) && ($preguntas<=100)) {
            $(location).attr('href', './cap/test/mercancia/aplicacion-reglamento/' + $preguntas);
        } else {
            $(".MA").html("Valores no válidos");
        }
        event.preventDefault();
    });

    $('#beginMercanciaMS').bind('click', function (event) {
        var $preguntas = $('.preguntasMS').val();
        if (($preguntas>=10) && ($preguntas<=100)) {
            $(location).attr('href', './cap/test/mercancia/salud-seguridad-logistica/' + $preguntas);
        } else {
            $(".MS").html("Valores no válidos");
        }
        event.preventDefault();
    });

    /****************************************************************************
     * Rutas de Viajeros
     ****************************************************************************/
    $('#beginViajeroVT').bind('click', function (event) {
        var $preguntas = $('.preguntasVT').val();
        if (($preguntas>=10) && ($preguntas<=100)) {
            $(location).attr('href', './cap/test/viajero/todas/' + $preguntas);
        } else {
            $(".VT").html("Valores no válidos");
        }
        event.preventDefault();
    });

    $('#beginViajeroVC').bind('click', function (event) {
        var $preguntas = $('.preguntasVC').val();
        if (($preguntas>=10) && ($preguntas<=100)) {
            $(location).attr('href', './cap/test/viajero/conduccion-racional/' + $preguntas);
        } else {
            $(".VC").html("Valores no válidos");
        }
        event.preventDefault();
    });

    $('#beginViajeroVA').bind('click', function (event) {
        var $preguntas = $('.preguntasVA').val();
        if (($preguntas>=10) && ($preguntas<=100)) {
            $(location).attr('href', './cap/test/viajero/aplicacion-reglamento/' + $preguntas);
        } else {
            $(".VA").html("Valores no válidos");
        }
        event.preventDefault();
    });

    $('#beginViajeroVS').bind('click', function (event) {
        var $preguntas = $('.preguntasVS').val();
        if (($preguntas>=10) && ($preguntas<=100)) {
            $(location).attr('href', './cap/test/viajero/salud-seguridad-logistica/' + $preguntas);
        } else {
            $(".VS").html("Valores no válidos");
        }
        event.preventDefault();
    });

    /****************************************************************************
    * Rutas de Comunes
     ****************************************************************************/
    $('#beginComunesCT').bind('click', function (event) {
        var $preguntas = $('.preguntasCT').val();
        if (($preguntas>=10) && ($preguntas<=100)) {
            $(location).attr('href', './cap/test/comunes/todas/' + $preguntas);
        } else {
            $(".CT").html("Valores no válidos");
        }
        event.preventDefault();
    });

    $('#beginComunesCC').bind('click', function (event) {
        var $preguntas = $('.preguntasCC').val();
        if (($preguntas>=10) && ($preguntas<=100)) {
            $(location).attr('href', './cap/test/comunes/conduccion-racional/' + $preguntas);
        } else {
            $(".CC").html("Valores no válidos");
        }
        event.preventDefault();
    });

    $('#beginComunesCA').bind('click', function (event) {
        var $preguntas = $('.preguntasCA').val();
        if (($preguntas>=10) && ($preguntas<=100)) {
            $(location).attr('href', './cap/test/comunes/aplicacion-reglamento/' + $preguntas);
        } else {
            $(".CA").html("Valores no válidos");
        }
        event.preventDefault();
    });

    $('#beginComunesCS').bind('click', function (event) {
        var $preguntas = $('.preguntasCS').val();
        if (($preguntas>=10) && ($preguntas<=100)) {
            $(location).attr('href', './cap/test/comunes/salud-seguridad-logistica/' + $preguntas);
        } else {
            $(".CS").html("Valores no válidos");
        }
        event.preventDefault();
    });
    /****************************************************************************
     * Rutas de búsqueda
     ****************************************************************************/
    $('#beginBuscar').bind('click', function (event) {
        //tipo puede ser pregunta y codigo
        //permitimos espacios en blanco pero lo pasamos con la sustitución del guión
        var $buscar = $('.buscarCS').val();
        var $tipo = $('#tipoBusqueda').val();

        // sólo aceptamos los carácteres indicados \s significa espaciado
        var expreg = /[^0-9A-Za-z_áéíóúñ,;:?¿\s]/;
        if(expreg.test($buscar)) {
            $(".buscar").html("Valores no válidos");
        } else {
            $buscar = $buscar.replace(/\s/g,"-");
            $(location).attr('href', './cap/test/buscar/' + $tipo + '/' + $buscar);
        }
        event.preventDefault();

    });
});
