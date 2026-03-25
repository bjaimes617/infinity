$(document).ready(function () {
    const rojo = '#FF0A0A';
    const verde = '#058203';
    const select = '#BE3A8D';
    // Manejador del evento click para los polylines
    $('polyline.seleccion').click(function () {
        $('#posicionsel').empty();
        var id = $(this).attr('id');
        
        ///se valida si el polilyne es de color diferente al seleccionado
        if ($('#' + id).attr('fill') !== select) {
            /// se valida si la posicion es de color rojo ya uqe esta ocupada
            if ($('#' + id).data('select') === 'X') {
                console.log("aca");
                $('#' + id).attr('fill', rojo);
                var Toastr = main();
                Toastr.fire({
                    icon: 'info',
                    title: "La posición Seleccionada ya esta ocupada.",
                });
            } else {
                 $('#' + id).attr('fill', select);
            }
           
        } else {
            if ($('#' + id).data('select') === 'X') {
                $('#' + id).attr('fill', rojo);
            } else {
                $('#' + id).attr('fill', verde);
            }
        }
        $('polyline.seleccion[fill="#BE3A8D"]').each(function () {
            var id = $(this).attr('id');           
            $('#posicionsel').append($('<option></option>').attr('value', id).attr('selected', 'selected').text($(this).data('sede') +'|'+ $(this).data('number')));            
        });
        
        
      
        //$('#posicionsel').append($("<option></option>").attr("value",$('#' + id)).text('AUT|'+$('#' + id)));  
    });
});