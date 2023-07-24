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
/*Esto hace el scroll para que al hacer click en el enlace para ir al lugar*/
$("#informacion").bind('click', function (event) {
    event.preventDefault();
    $('html, body').animate({
        scrollTop: $('.informacion').offset().top
    }, 1000);
});
$("#tipo").bind('click', function (event) {
    event.preventDefault();
    $('html, body').animate({
        scrollTop: $('.tipo').offset().top
    }, 1000);
});
$("#uso").bind('click', function (event) {
    event.preventDefault();
    $('html, body').animate({
        scrollTop: $('.uso').offset().top
    }, 1000);
});
$("#eliminar").bind('click', function (event) {
    event.preventDefault();
    $('html, body').animate({
        scrollTop: $('.eliminar').offset().top
    }, 1000);
});
$("#usuarios").bind('click', function (event) {
    event.preventDefault();
    $('html, body').animate({
        scrollTop: $('.usuarios').offset().top
    }, 1000);
});
$("#privacidad").bind('click', function (event) {
    event.preventDefault();
    $('html, body').animate({
        scrollTop: $('.privacidad').offset().top
    }, 1000);
});
$("#responsabilidad").bind('click', function (event) {
    event.preventDefault();
    $('html, body').animate({
        scrollTop: $('.responsabilidad').offset().top
    }, 1000);
});

})