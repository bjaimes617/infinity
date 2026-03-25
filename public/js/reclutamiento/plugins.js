if ($('.bs-stepper').length > 0) {
    document.addEventListener('DOMContentLoaded', function () {
        window.stepper = new Stepper(document.querySelector('.bs-stepper'), {
            linear: false
        });
    });
}

$.validator.addMethod("valoresCantidadHijos", function (value, element) {
    // Obtener el valor de cantidadhijos
    var cantidad = parseInt($('#nrodehijos').val(), 10);

    if (isNaN(cantidad) || cantidad <= 0) {
        // Si cantidadhijos no es un número válido, dejamos pasar la validación aquí
        return true;
    }
    // Dividir el valor del input separado por comas y filtrar elementos vacíos
    var valores = value.split(',').map(function (item) {
        return item.trim();
    }).filter(function (item) {
        return item.length > 0;
    });

    // Validar que la cantidad de valores sea igual a cantidadhijos
    return valores.length === cantidad;

}, "Los Valores separados por comas debe coincidir con la cantidad de hijos.");

/// validacion del formulario modal preempleo
var validation = function () {

    var ficha = function () {
        $('#infoparticipante').validate({
            ignore: [],
            rules: {
                edadhijos: {
                    valoresCantidadHijos: true
                }
                , sexohijos: {
                    valoresCantidadHijos: true
                },
                edad: {
                    min: 18
                }
            },
            lang: 'es',
            errorElement: 'span',
            validClass: "is-valid",
            errorPlacement: function (error, element) {
                error.addClass('is-invalid text-sm');
                element.closest('.form-group').append(error);
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid text-sm');
                $('.step button').find('small.required').remove();
                var validatePane = $('.bs-stepper-content .content:has(.is-invalid)').each(function () {
                    var id = $(this).attr('id');
                    $('.step').find('button[aria-controls="' + id + '"]').find('span.bs-stepper-label').after(' <small class="required" style="color:red;"> <b>***</b></small>');
                });
                var Toastr = main();
                Toastr.fire({
                    icon: 'error',
                    html: " Debe completar los campos obligatorios en las fichas marcas con <span style='color:red;' > *** </span> "});

            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('error is-invalid text-sm');
            },
            submitHandler: function (form) {
                Swal.fire({
                    title: 'Certificacion de Datos',
                    html: 'Para los efectos legales, certifico que todas las respuestas e información proporcionada por mí son veraces, completas y corresponden a la realidad. <br>\n\
                    <b>ATENCION:</b> Los Datos aqui ingresados seran corroborados el día de la entrevista.',
                    icon: 'info',
                    showCancelButton: true,
                    showConfirmButton: true,
                    cancelButtonText: 'Cancelar',
                    confirmButtonText: 'Confirmar',
                }).then((result) => {     // Se agrega el método .then()
                    if (result.isConfirmed) {    // Aquí se valida si se presionó 'Cancelar'
                        var inputs = form.getElementsByTagName('input');
                        for (var i = 0; i < inputs.length; i++) {
                            // Verificar si el input está deshabilitado
                            if (inputs[i].disabled) {
                                // Cambiar a readonly
                                inputs[i].disabled = false; // Primero habilitar el input
                                inputs[i].readOnly = true;   // Luego establecerlo como readonly
                                console.log(inputs[i]);
                            }
                        }
                        var select = form.getElementsByTagName('select');
                        for (var i = 0; i < select.length; i++) {
                            // Verificar si el input está deshabilitado
                            if (select[i].disabled) {
                                // Cambiar a readonly
                                select[i].disabled = false; // Primero habilitar el input
                                select[i].readOnly = true;   // Luego establecerlo como readonly
                                console.log(select[i]);
                            }
                        }

                        form.submit();
                    }
                });
            }
        });
    };
    return {
        // public functions
        init: function () {
            ficha();
        }
    };
}();

///busqueda tabla preselccion
$('#ShowCandidatos').validate({
    ignore: [],    
    lang: 'es',
    errorElement: 'span',
    validClass: "is-valid",
    errorPlacement: function (error, element) {
        error.addClass('is-invalid text-sm');
        element.closest('.form-group').append(error);
    },
    highlight: function (element, errorClass, validClass) {
        $(element).addClass('is-invalid text-sm');
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass('error is-invalid text-sm');
    },
    submitHandler: function (form) {
        event.preventDefault();
        $.ajax({
            type: form.method,
            url: form.action,
            data: $(form).serialize(),
            dataType: 'json',
            success: function (data) {
                
                if ($.fn.dataTable.isDataTable('#datatable-prospectos')) {
                    $('#tablelistado').fadeOut();
                    $('#datatable-prospectos').DataTable().destroy();
                }
                     $('#datatable-prospectos').DataTable({
                    "initComplete": function (settings, json) {
                        $('#tablelistado').fadeIn();
                    },
                    "responsive": true,
                    "ordering": false,
                    "autoWidth": false,
                    "lengthMenu": [20, 50, 100, 200],
                    "bPaginate": true,
                    "bProcessing": true,
                    data: data,                  
                    dom: 'Blfrtip',
                        buttons: [
                            'copy', 'csv', 'excel'
                    ],
                    "columns": [
                        {mData: 'cedula'},
                        {mData: 'nombre'},
                        {mData: 'edad'},
                        {mData: 'experiencia'},
                        {mData: 'cargo'},                        
                        {mData: 'detalle'},
                        {mData: 'celular'},
                        {mData: 'habitacion'},
                        {mData: 'email'},
                        {mData: 'serviciosanteriores'},
                        {mData: 'direccion'},
                        {mData: 'acciones'},
                        
                    ]
                });
                
            }, error: function (data) {              
                var Toastr = main();
                Toastr.fire({
                    icon: 'error',
                    title: data.responseJSON.message,
                });
            }
        });
    }
});

///busqueda tabla preselccion
$('#FromReportes').validate({
    ignore: [],    
    lang: 'es',
    errorElement: 'span',
    validClass: "is-valid",
    errorPlacement: function (error, element) {
        error.addClass('is-invalid text-sm');
        element.closest('.form-group').append(error);
    },
    highlight: function (element, errorClass, validClass) {
        $(element).addClass('is-invalid text-sm');
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass('error is-invalid text-sm');
    },
    submitHandler: function (form) {
        
        form.submit();
    }
});
$(document).ready(function () {


    if ($('.daterange').length !== 0) {

        $('.daterange').daterangepicker({
            showDropdowns: false,
            maxDate: moment().format('DD/MM/YYYY'),
            locale: {
                format: 'DD/MM/YYYY',
            }
        });
    }

    if ($('.dateter').length !== 0) {

        $('.dateter').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            maxDate: moment().format('DD/MM/YYYY'),
            locale: {
                format: 'DD/MM/YYYY',
            }
        });
        // Al cargar el DOM, dejamos el input vacío
        $('.dateter').val('');
        // Opcional: si quieres que al seleccionar una fecha se actualice el input       
    }

    if ($('.dateyears').length !== 0) {
        $('.dateyears').datetimepicker({
            format: "YYYY",
            "maxDate": moment(),

        });
        // Al cargar el DOM, dejamos el input vacío
        $('.dateyears').val('');
        // Opcional: si quieres que al seleccionar una fecha se actualice el input       
    }

    $('.input-number').on('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    if ($('.table').length !== 0) {
        $(".table").DataTable({
            "lengthMenu": [10, 20, 15, 30, 50, 60],
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "ordering": true,
            //"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        });
    }

    if ($('.time').lentgh > 0) {
        $('.time').datetimepicker({
            format: 'hh:mm a'
        });
    }

    if ($('.select2').lentgh > 0) {
        $('.select2').select2({theme: 'bootstrap4'});
    }
    //validation.init();
});

var loadFile = function (event) {
    var image = document.getElementById('previewimg');
    image.src = URL.createObjectURL(event.target.files[0]);
};

$('.conteoEdades').on('input', function () {
    // Obtener el valor actual del input
    let valor = $(this).val();
    // Reemplazar espacios por comas
    valor = valor.replace(/[^0-9]+/g, ',');
    // Actualizar el valor del input
    $(this).val(valor);
});

$('.conteoGeneros').on('input', function () {
    // Obtener el valor actual del input
    let valor = $(this).val();
    // Reemplazar cualquier carácter que no sea 'M' o 'F' por comas
    valor = valor.replace(/[^MmFf]+/g, ',');

    // Actualizar el valor del input
    $(this).val(valor);
});

$('#edocivil').on('change', function () {
    $(this).val() === "C" ? $('#conyuge').attr('required', 'required') : $('#conyuge').removeAttr('required', 'required');
});

$('#trabajoantes').on('change', function () {
    $(this).val() === "S" ?
            ($('.fechaanterior').show(), $('#fechantes').attr('required', 'required')) :
            ($('.fechaanterior').hide(), $('#fechantes').removeAttr('required', 'required'));
});

$('#parientes').on('change', function () {
    $(this).val() === "S" ?
            ($('.displaymencionelos').show(), $('#mencionelos').attr('required', 'required')) :
            ($('.displaymencionelos').hide(), $('#mencionelos').removeAttr('required', 'required'));
});

$('#nacimiento').on('change', function () {
    console.log("ejecuta");
    var fechanacimiento = moment($(this).val(), 'DD/MM/YYYY'); // Asegúrate de que el formato sea correcto
    var fechaactual = moment(); // Fecha actual
    // Calcular la edad
    var edad = fechaactual.diff(fechanacimiento, 'years'); // Diferencia en años
    $('#edad').val(edad);
    //console.log('Fecha de nacimiento:', fechanacimiento.format('DD/MM/YYYY'));
    //console.log('Edad:', edad);
});

var Validaciones =  function () {
    
    var previewModal = function () {

        $('.btn-group-toggle input[type=radio]:checked').each(function () {
            $(this).closest('label').addClass('active');
        });

        $('.select2').select2({theme: 'bootstrap4'});

        $('.input-number').on('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        $('.dateter').each(function () {
            if ($(this).val() !== "") {
                $(this).daterangepicker({
                    singleDatePicker: true,
                    showDropdowns: true,
                    maxDate: moment().format('DD/MM/YYYY'),
                    locale: {
                        format: 'DD/MM/YYYY',
                    }
                });
            } else {
                $(this).daterangepicker({
                    singleDatePicker: true,
                    showDropdowns: true,
                    maxDate: moment().format('DD/MM/YYYY'),
                    locale: {
                        format: 'DD/MM/YYYY',
                    }
                });
                $(this).val('');
            }
        });


        $('.conteoEdades').on('input', function () {
            // Obtener el valor actual del input
            let valor = $(this).val();
            // Reemplazar espacios por comas
            valor = valor.replace(/[^0-9]+/g, ',');
            // Actualizar el valor del input
            $(this).val(valor);
        });

        $('.conteoGeneros').on('input', function () {
            // Obtener el valor actual del input
            let valor = $(this).val();
            // Reemplazar cualquier carácter que no sea 'M' o 'F' por comas
            valor = valor.replace(/[^MmFf]+/g, ',');

            // Actualizar el valor del input
            $(this).val(valor);
        });

        $('#edocivil').on('change', function () {
            $(this).val() === "C" ? $('#conyuge').attr('required', 'required') : $('#conyuge').removeAttr('required', 'required');
        });

        $('#trabajoantes').on('change', function () {
            $(this).val() === "S" ?
                    ($('.fechaanterior').show(), $('#fechantes').attr('required', 'required')) :
                    ($('.fechaanterior').hide(), $('#fechantes').removeAttr('required', 'required'));
        });

        $('#parientes').on('change', function () {
            $(this).val() === "S" ?
                    ($('.displaymencionelos').show(), $('#mencionelos').attr('required', 'required')) :
                    ($('.displaymencionelos').hide(), $('#mencionelos').removeAttr('required', 'required'));
        });

        $('#nacimiento').on('change', function () {
            console.log("ejecuta");
            var fechanacimiento = moment($(this).val(), 'DD/MM/YYYY'); // Asegúrate de que el formato sea correcto
            var fechaactual = moment(); // Fecha actual
            // Calcular la edad
            var edad = fechaactual.diff(fechanacimiento, 'years'); // Diferencia en años
            $('#edad').val(edad);
            //console.log('Fecha de nacimiento:', fechanacimiento.format('DD/MM/YYYY'));
            //console.log('Edad:', edad);
        });

    };
    var fromValidate = function () {
        $('#updateinfoparticipante').validate({
            ignore: [],
            lang: 'es',
            errorElement: 'span',
            validClass: "is-valid",
            errorPlacement: function (error, element) {
                error.addClass('is-invalid text-sm');
                element.closest('.form-group').append(error);
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid text-sm');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('error is-invalid text-sm');
            },
            submitHandler: function (form) {

                Swal.fire({
                    title: '¡Atención!',
                    html: 'Se procedera a actualizar los Datos de la planilla de Pre-empleo. ¿Desea Continuar?',
                    icon: 'info',
                    showCancelButton: true,
                    showConfirmButton: true,
                    cancelButtonText: 'Cancelar',
                    confirmButtonText: 'Confirmar',
                }).then((result) => {     // Se agrega el método .then()
                    if (result.isConfirmed) {    // Aquí se valida si se presionó 'Cancelar'

                        var inputs = form.getElementsByTagName('input');
                        for (var i = 0; i < inputs.length; i++) {
                            // Verificar si el input está deshabilitado
                            if (inputs[i].disabled) {
                                // Cambiar a readonly
                                inputs[i].disabled = false; // Primero habilitar el input
                                inputs[i].readOnly = true;   // Luego establecerlo como readonly
                                console.log(inputs[i]);
                            }
                        }
                        var select = form.getElementsByTagName('select');
                        for (var i = 0; i < select.length; i++) {
                            // Verificar si el input está deshabilitado
                            if (select[i].disabled) {
                                // Cambiar a readonly
                                select[i].disabled = false; // Primero habilitar el input
                                select[i].readOnly = true;   // Luego establecerlo como readonly
                                console.log(select[i]);
                            }
                        }

                        form.submit();
                    }
                });
            }
        });
    };
    var PasetoCurso = function () {
        $('#AprobadoaACurso').validate({
            ignore: [],
            lang: 'es',
            errorElement: 'span',
            validClass: "is-valid",
            errorPlacement: function (error, element) {
                error.addClass('is-invalid text-sm');
                element.closest('.form-group').append(error);
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid text-sm');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('error is-invalid text-sm');
            },
            submitHandler: function (form) {

                Swal.fire({
                    title: '¡Atención!',
                    html: 'EL candidato sera asignado al Curso seleccionado, tenga en cuenta que la planilla de empleo no estara disponible ¿Desea Continuar?',
                    icon: 'info',
                    showCancelButton: true,
                    showConfirmButton: true,
                    cancelButtonText: 'Cancelar',
                    confirmButtonText: 'Confirmar',
                }).then((result) => {     // Se agrega el método .then()
                    if (result.isConfirmed) {    // Aquí se valida si se presionó 'Cancelar'
                        event.preventDefault();
                        $.ajax({
                            type: form.method,
                            url: form.action,
                            data: $(form).serialize(),
                            dataType: 'json',
                            success: function (data) {
                               
                                var row= $('#candidatorow').val();
                                 console.log(row);
                                $('#candidato'+row).fadeOut();
                                
                                 var Toastr = main();
                                Toastr.fire({
                                    icon: 'success',
                                    title: data.message,
                                });
                                
                                $('#ModalAceptCurso').modal('toggle');  
                                
                            }, error: function (data) {
                                var Toastr = main();
                                Toastr.fire({
                                    icon: 'error',
                                    title: data.responseJSON.message,
                                });
                            }
                        });
                    }
                });
            }
        });
    };
    return {
        condiciones: function () {
            previewModal();
        },
        init: function () {
            fromValidate();
        },
        PasetoCurso: function(){
            PasetoCurso();
        }
    };
}();

function ShowPlanillaProspecto(row) {

    var url = $('#' + row).data('href');
    var data = {
        _token: $("input[name=_token]").val(),
    };

    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: "JSON",
        success: function (data) {

            $('#appenModaldinputs').empty();
            $('#appenModaldinputs').append(data);

            Validaciones.condiciones();
            $('#updateinfoparticipante').attr('action', $('#' + row).data('puthref'));

            Validaciones.init();

            $('#modal-xl').modal('show');

        }, error: function (data) {
            var Toastr = main();
            Toastr.fire({
                icon: 'error',
                title: data.responseJSON.message,
            });
        }
    });

}

function ShowCursoAvailable(candidato){
    
    $('#ModalAceptCurso').modal('show');    
    $('#candidatorow').val(candidato);
    $('#AprobadoaACurso').attr('action', $('#ButtonCurso' + candidato).data('aprobacion'));    
    Validaciones.PasetoCurso();
}