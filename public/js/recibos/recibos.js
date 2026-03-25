$(function () {
    if ($('.custom-file-input').length != 0) {
        bsCustomFileInput.init();
    };
    
    if ($('.dateter').length != 0) {
        $('.dateter').datetimepicker({
            format: 'DD/MM/YYYY',
            maxDate: moment(), 
        });
     }
    
    if ($('.mesdate').length != 0) {
        $('.mesdate').datetimepicker({
            format: 'MM/YYYY',
            maxDate: moment(), 
        });   
    }
   
    $('.input-number').on('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
});

function disable(value) {
    if ($('#tipocarga').val() === '') {
        var Toastr = main();
        $(function () {
            Toastr.fire({
                icon: "error",
                title: "&nbsp; Debes Seleccionar que tipo de archivo se cargara.",
            });
        });
    } else {
        $('.cargamasiva').attr('disabled', 'disabled');

        if (validador_file() === true) {            
            
            var validator = $('#create-agent').validate({errorPlacement: function (error, element) {}});            
            var formulario = $('#create-agent');
            var isValid = $('#create-agent').valid();
            if (!isValid) {
                validator.focusInvalid();
                event.preventDefault();
                $('.cargamasiva').removeAttr('disabled');
            } else {
                $.blockUI({message: '<h4><img style="width: 100px; height: 100px;" src="' + asset + '" />\n\
                <p>Espere unos instantes, mientras Procesamos...</p></h4>'});
                formulario.submit();
            }

        }
    }
}

function validador_file() {
    var file = $('input[type="file"]');
    var ext = file.val().split('.').pop();
    if (file.val() !== '') {
        if (!['xls', 'xlsx'].includes(ext)) {
            file.val('');
            var variable = "error",
                    mensaje = "No se permite archivos de tipo " + ext;

            var Toastr = main();
            $(function () {
                Toastr.fire({
                    icon: variable,
                    title: "&nbsp;" + mensaje,
                });
            });
            $('.cargamasiva').removeAttr('disabled');
            return false;
        } else {
            return true;
        }
    } else {
        var variable = "error",
                mensaje = "Debe adjuntar un archivo de tipo xls o xlsx";
        var Toastr = main();
        $(function () {
            Toastr.fire({
                icon: variable,
                title: "&nbsp;" + mensaje,
            });
        });
        $('.cargamasiva').removeAttr('disabled');
        return false;
    }
}

$('#nomina').on('change', function () {
    
   var selectedOption = $(this).val();    
    $('#tprecibo option').hide(); // Oculta todas las opciones primero    
    // Muestra la opción sin valor
    $('#tprecibo option[value=""]').show();    
    // Muestra las opciones que coinciden con el valor seleccionado
    $('#tprecibo option[data-nomina="' + selectedOption + '"]').show();    
    $('#tprecibo').val('');
    $('#tprecibo').removeAttr('disabled');
});

$('#tprecibo').on('change', function () {
    
    var selectedOption = $(this).val();
    $('.mesdate').datetimepicker('destroy');
    if (["90", "90A"].includes(selectedOption)) {

        $('.mesdate').datetimepicker({
            format: 'YYYY',
            maxDate: moment(),
        });
    } else {
        $('.mesdate').datetimepicker({
            format: 'MM/YYYY',
            maxDate: moment(),
        });
    }
    
    $('#corte option').show();    
    $('#corte').val('');  
    const optionsToHide = {
        "01": ["4"], /// empleado
        "91": ["1", "2", "3"], ////vacaciones
        "81": ["1", "2", "3"],  /// Cestatikets
        "90": ["1", "2", "3"],  /// UTILIDADES
        "90A": ["1", "2", "3"]  /// UTILIDADES
        
    };

    if (optionsToHide[selectedOption]) {
        optionsToHide[selectedOption].forEach(function(value) {
            console.log(value);
            $('#corte option[value="' + value + '"]').hide();
        });
    }
});

$('#create-recibo').validate({
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