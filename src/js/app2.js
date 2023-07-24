$(document).foundation()
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
})
