$(function () {
    
    if ($('.anioactual').length != 0) {
        $('.anioactual').datetimepicker({
            "format": 'YYYY',
            "minDate": moment().year(-1),
            "maxDate": moment()
        });
    }
    
    $('.input-number').on('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
    
    if ($('.table').length != 0) {
        $(".table").DataTable({
            "lengthMenu": [10, 20, 15, 30, 50, 60],
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "ordering": false,
            //"order":[8,"ASC"]
            //"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        });
        if ($('.unidadactive').length != 0) {
            
            $('.unidadactive').bootstrapSwitch({offText: "Off", onText: "On"});
            ///aplica a elementos de paginacion
            $('.table').on('draw.dt', function () {
                $(document).on('bootstrapSwitch').bootstrapSwitch({offText: "Off", onText: "On"});
            });
        }
    }
    
    if ( $('.select2').length != 0){
        $('.select2').select2({theme: 'bootstrap4'});
    }

});

$('#create-ut').validate({
    lang: 'es',
    errorElement: 'span',
    errorPlacement: function (error, element) {
        error.addClass('is-invalid text-sm');
        element.closest('.form-group').append(error);
    },
    highlight: function (element, errorClass, validClass) {
        $(element).addClass('is-invalid');
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass('is-invalid');
    },
    submitHandler: function (form) {
        form.submit();
    }
});

$('#generate-ari').validate({
    lang: 'es',
    errorElement: 'span',
    errorPlacement: function (error, element) {
        error.addClass('is-invalid text-sm');
        element.closest('.form-group').append(error);
    },
    highlight: function (element, errorClass, validClass) {
        $(element).addClass('is-invalid');
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass('is-invalid');
    },
    submitHandler: function (form) {
        form.submit();
    }
});

$(document).on('switchChange.bootstrapSwitch', '.unidadactive', function (event, state) {
    // Evitar que el evento se dispare nuevamente si ya se ha confirmado o cancelado  
    
    if (state === true) {
        var mensajes = 'Esta por activar el periodo ARI, el valor de la unidad tributaria no podrá ser modificado ¿Desea continuar?';
        var url =  $(this).data("href") + "/1";
        var revertir = true;
        
    } else {
        var mensajes = 'Esta por desactivar el periodo ARI, No pordra acceder a la planilla ARI. ¿Desea Continuar?.';
        var url =  $(this).data("href") + "/0";
        var revertir = false;
    }
    
      Swal.fire({
            title: '¡Atención!',
            text: mensajes,
            icon: "question",
            showCancelButton: true,
            showConfirmButton: true,
            cancelButtonText: 'Cancelar',
            confirmButtonText: 'Confirmar',
        }).then((result) => {
            if (result.isConfirmed) {
                // Solo desactivar el input si el estado cambia de false a true   
                //$(this).bootstrapSwitch('disabled', true); // Desactivar el switch
                $(this).attr('disabled', 'disabled'); // Desactivar el input  
                var data = {_token: $("input[name='_token']").val()};
                $.ajax({
                    type: 'PUT',
                    url: url,
                    data: data,
                    dataType: 'json',
                    success: function (data)
                    {
                        var Toastr = main();
                        $(function () {
                            Toastr.fire({
                                icon: data.type,
                                title: "&nbsp;" + data.message,
                            });
                        });
                    }
                });
            } else {
                if(revertir){
                    $(this).bootstrapSwitch('state', false, true); // Cambiar el estado a true
                } else {
                    $(this).bootstrapSwitch('state', true, true);
                }
                // Si se cancela, revertir el estado a false
               

            }
        });
});


///cambios par reportes
$('#area_trabajo').on('change', function () {
    
    var data = {
        _token: $('input[name="_token"]').val(),
        area: $(this).val()
    };
    var url = $(this).data('href');
  
    $.ajax({
        type: 'POST',
        url: url,
        data: data,
        dataType: 'json',
        success: function (data)
        {
            $('#carteras').empty();
            $('#carteras').append($("<option></option>").attr("value", '').text("[TODOS]"));
            $('#skill').empty();
            $('#skill').append($("<option></option>").attr("value", '').text("[TODOS]"));
            for (i = 0; i < data.length; i++)
            {
                $('#carteras').append($("<option></option>").attr("value", data[i].id).text(data[i].nombre_cartera));
            }
        },
        error: function (data) {          
            var Toastr = main();
            Toastr.fire({
                icon: 'error',
                title: data.responseJSON.message,
            });
        }
    });
});

$('#generate-report').validate({
    lang: 'es',
    errorElement: 'span',
    errorPlacement: function (error, element) {
        error.addClass('is-invalid text-sm');
        element.closest('.form-group').append(error);
    },
    highlight: function (element, errorClass, validClass) {
        $(element).addClass('is-invalid');
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass('is-invalid');
    },
    submitHandler: function (form) {
        form.submit();
    }
});
