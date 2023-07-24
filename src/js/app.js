$(document).foundation();
// elimina información de las cookies
function remover(){
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

    //indica si está marcado o no el elemento
    var marcado=[];
    var aciertos = 0;
    var fallos = 0;
    var nulas;
    var total = $("#nulas").val();
    nulas = total;
    var result = 0;

    $("#crearExamen").bind('click', function (event) {
        var actual = $("#inicio").attr("action");
        indice = actual.indexOf("grabar-datos");
        indice = actual.substr(0,indice) + "crear-examen";
        $("#inicio").attr("action", indice);
    });

    /*Esto hace el scroll para que al hacer click en el enlace para ir arriba*/
    $("#arriba").bind('click', function (event) {
        event.preventDefault();
        volver  = $(this).attr('href');
        $('html, body').animate({
            scrollTop: $(volver).offset().top
        }, 1000);
    });

    $(":radio").bind('click', function (event) {
        // recuperamos los datos del elemento marcado
        // var id = $(this).attr("id");
        var respuesta = $(this).val();
        var numeroPregunta = $(this).attr("data");
        var solucion = $("#solucion" + numeroPregunta).val();
        console.log('respuesta '+respuesta);
        console.log('solucion '+solucion);
        console.log('numeropregunta '+numeroPregunta);
        // Aquí es donde compruebo si ya está marcado y en ese caso lo anulo.
        // También doy la información al usuario si ha acertado o no
        if (marcado[numeroPregunta] != null){
            event.preventDefault();
            $(this).blur();
        } else {
            if (respuesta.trim() == solucion.trim()) {
                $("#e" + numeroPregunta + respuesta).css("color", "green");
                $("#respuesta" + numeroPregunta).css("color", "green");
                $("#respuesta" + numeroPregunta).text("** Perfecto **");
                aciertos = aciertos +1;
                nulas = nulas - 1;

                result = result + 100/total;
                $("#aciertos").val(aciertos);
            } else {
                $("#e" + numeroPregunta + respuesta).css("color", "red");
                $("#respuesta" + numeroPregunta).css("color", "red");
                $("#respuesta" + numeroPregunta).text("Solución: " + solucion);
                fallos = fallos + 1;
                nulas = nulas - 1;

                result = result - 50/total;
                $("#fallos").val(fallos);
            }
            $("#nulas").val(nulas);

            if (result<0) {
                $("#nota").val(0);
            } else {
                result2 = result.toFixed(2);
                Buscarpunto = result2.indexOf('.');
                Parte1 = result2.substring(0, Buscarpunto)
                Parte2 = result2.substring(Buscarpunto + 1, result2.length)
                final = Parte1 + "," + Parte2;

                $("#nota").val(final);
            }

            marcado[numeroPregunta] = respuesta;
        }
    });

    /* Reloj */
    momentoInicial = new Date();
    var  diferencia, minuto;

    setInterval(clock, 1000);
    function clock() {
        momentoActual = new Date();

        diferencia = momentoActual.getTime() - momentoInicial.getTime();
        minuto = Math.floor(diferencia / (1000 * 60));
        /*
        str_minuto = new String (minuto);
        if (str_minuto.length == 1)
            minuto = "0" + minuto;

        horaImprimible = minuto + ' minuto';
        if (minuto>1)
            horaImprimible = minuto + ' minutos';

        horaImprimible = minuto;
        */
        $('.reloj').val(minuto);
    }

});