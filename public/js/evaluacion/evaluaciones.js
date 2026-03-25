$(function () {
    if ($('.select2').length != 0) {
        $('.select2').select2({
            theme: 'bootstrap4',
        });
    }

    ///crear y actualizar indicadores
    $('#indicadores_store').length !== 0 ? Validacion.CreateEvaluacion() : Validacion.UpdateEvaluacion();
    ///crear evaluaciones
    if ($('.mesdate').length !== 0) {
        // Restar 7 días a la fecha actual        
        $('.mesdate').datetimepicker({
            format: 'MM/YYYY',
            minDate: moment().subtract(7, 'days'),
            maxDate: moment(),
        });
    }
    
     if ($('.rangofechas').length !== 0) {
        // Restar 7 días a la fecha actual        
        $('.rangofechas').daterangepicker({       
        locale:{
            format: 'DD/MM/YYYY', 
        },       
        maxDate: moment(),
    });
    }

    ///validador para crear y evaluar personal
    $('#CreateEvaluacionPersonal').validate({
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

    ///validador para crear respuestas de la evaluacion 
    $('#indicador_form').validate({
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
            var personal_id = $("#editpersonal_id").val();
            $.ajax({
                type: form.method,
                url: form.action,
                data: $(form).serialize(),
                dataType: 'json',
                success: function (data)
                {
                    var Toastr = main();
                    Toastr.fire({
                        icon: 'success',
                        title: '&nbsp;' + data.message,
                    });
                    $('#editpersonal').modal('hide'); // Cerrar el modal
                    $('#indicador_form')[0].reset(); // Limpiar el formulario
                    $('#personal' + personal_id).attr("disabled", "disabled");
                    $('#personal' + personal_id).removeClass('btn-primary'); // Quitar la clase btn-danger
                    $('#personal' + personal_id).addClass('btn-success');   // Agregar la clase btn-success
                    $('#personal' + personal_id).html('<i class="fa fa-headphones"></i> Evaluado');
                },
                error: function (data) {

                    var Toastr = main();
                    Toastr.fire({
                        icon: 'error',
                        title: data.responseJSON.message,
                    });

                    $('#editpersonal').modal('hide'); // Cerrar el modal
                }
            });
        }
    });

    $('#UpdateIndicadores').validate({
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
                success: function (data)
                {
                    $('#editeval').modal('hide');
                    form.reset;
                    var Toastr = main();
                    Toastr.fire({
                        icon: 'success',
                        title: '&nbsp;' + data.message,
                    });

                },
                error: function (data) {
                    $('#editeval').modal('hide');
                    var Toastr = main();
                    Toastr.fire({
                        icon: 'error',
                        title: data.responseJSON.message,
                    });
                }
            });
        }
    });

///evaluaciones - administracion - registrados
    $("#ShowEvaluaciones").validate({
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

                    if ($.fn.dataTable.isDataTable('#datatable-evaluaciones')) {
                        $('#tablelistado').fadeOut();
                        $('#datatable-evaluaciones').DataTable().destroy();
                    }

                    $('#datatable-evaluaciones').DataTable({
                        "initComplete": function (settings, json) {
                            $('#tablelistado').fadeIn();
                        },
                        responsive: true,
                        "ordering": false,
                        "autoWidth": false,
                        data: data,
                        "lengthMenu": [20, 50, 75],
                        "bPaginate": true,
                        "bProcessing": true,
                        "columns": [
                            {mData: 'gerencia'},
                            {mData: 'carteras'},
                            {mData: 'skill'},
                            {mData: 'cargo'},
                            {mData: 'cantidad'},
                            {mData: 'tipo'},
                            {mData: 'estatus'},
                            {mData: 'acciones'},
                        ]
                    });

                    $('.periodoactive').bootstrapSwitch({offText: "Off", onText: "On"});

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

    $('#create-report').validate({
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

    if ($('#Tablevaluaciones').length !== 0) {

        $('#Tablevaluaciones').DataTable({
            responsive: true,
            "ordering": false,
            "autoWidth": false,
            "lengthMenu": [20, 25, 50, 75],
            "bPaginate": true,
            "bProcessing": true,
        });
    }
});

$('#evaluacion_masiva').validate({
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
    
if ($('#evaluacion_masiva').length !== 0) {
    $(function () {
        bsCustomFileInput.init();
    });

}

///tabla de evaluaciones
$(function () {
    $('.table').on('click', '.editpersonal', function (event) {
        event.preventDefault();
        $('#editnombre_personal').val($(this).data("nombre_personal"));
        $('#editpersonal_id').val($(this).data("personal_id"));
        $('#editcargo_id').val($(this).data("cargo_id"));
        $('#editskill_id').val($(this).data("skill_id"));
        $('#editpersonal').modal('show');
    });
});

$('#area_trabajo').on('change', function () {
    var url = $(this).data('href');
    var data = {area: $(this).val(), _token: $("input[name=_token]").val(), };
    $('#carteras').empty();
    $.ajax({
        type: 'POST',
        url: url,
        data: data,
        dataType: 'json',
        success: function (data) {
            var value = "[SELECCIONE]";
            $('#carteras').append($("<option></option>").attr("value", '').text(value));
            for (i = 0; i < data.length; i++)
            {
                $('#carteras').append($("<option></option>").attr("value", data[i].id).text(data[i].nombre_cartera));
            }
        }, error: function () {
            var Toastr = main();
            Toastr.fire({
                icon: 'error',
                title: "&nbsp; Opción Invalida.",
            });
        }
    });
});

$('#carteras').on('change', function () {
    var url = $(this).data('href');
    var data = {cartera: $(this).val(), _token: $("input[name=_token]").val(), };
    $('#skill').empty();
    $.ajax({
        type: 'POST',
        url: url,
        data: data,
        dataType: 'json',
        success: function (data)
        {
            var value = "[SELECCIONE]";
            $('#skill').append($("<option></option>").attr("value", '').text(value));
            for (i = 0; i < data.length; i++)
            {
                $('#skill').append($("<option></option>").attr("data-codcecos", data[i].cod_ceco).attr("value", data[i].id).text(data[i].nombre_skill));
            }
        }, error: function (data) {
            var Toastr = main();
            Toastr.fire({
                icon: 'error',
                title: data.responseJSON.message,
            });
        }
    });
});

$('#skill').on('change', function () {

    var url = $(this).data('href');
    var data = {
        area: $('#area_trabajo').val(),
        cartera: $('#carteras').val(),
        skill: $('#skill').val(),
        _token: $("input[name=_token]").val()
    };
    $.ajax({
        type: 'POST',
        url: url,
        data: data,
        dataType: 'json',
        success: function (data)
        {
            $('#cargo').empty();
            $('#cargo').append($("<option></option>").attr("value", '').text("[SELECCIONE]"));
            for (i = 0; i < data.length; i++)
            {
                $('#cargo').append($("<option></option>").attr("value", data[i].id).text(data[i].nombre_cargo));
            }

        }, error: function (data) {
            var Toastr = main();
            Toastr.fire({
                icon: 'error',
                title: data.responseJSON.message,
            });
        }
    });
});

$('#cargo').on('change', function () {

    if ($('#CreateEvaluacionPersonal').length !== 0 || $('#evaluacion_masiva').length !== 0) {

        var data, url;
        url = $(this).data('href');
        data = {
            mes: $('#fechaeval').val(),
            cargo_eval: $(this).val(),
            skill_asig: $("#skill").val(),
            _token: $("input[name='_token']").val(),
            carteras: $("#carteras").val(),
            area_trabajo: $("#area_trabajo").val(),
        };

        $.ajax({
            type: 'POST',
            url: url,
            data: data,
            dataType: 'json',
            success: function (data)
            {
                if (data === null || data[0] === null) {
                    var Toastr = main();
                    Toastr.fire({
                        icon: 'error',
                        title: 'El cargo y skill seleccionado no tienen indicadores registrados',
                    });                     
                    $('button[type="submit"]').prop('disabled',true);   
                    $('button[type="button"]').prop('disabled',true);   
                   
                } else {
                $('button[type="submit"]').prop('disabled',false);  
                $('button[type="button"]').prop('disabled',false);   
                
                $('#Tipoperiodo').html('');
                $('#Tipoperiodo').html(data[0]);

                $('label[for="fechaeval"]').html(data[0] !== null ?
                'Fecha de Evaluacion ' + data[0].toUpperCase() + '<span style="color:red">*</span>' :
                        'Fecha de Evaluacion <span style="color:red">*</span>');

                Activatedatapicker(data[0],data); 
                }
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

$('#fechaeval').on('blur', function () {

    var url = $(this).data('href');
    var data = {
        mes: $(this).val(),
        cargo_eval: $('#cargo').val(),
        skill_asig: $("#skill").val(),
        _token: $("input[name='_token']").val(),
        carteras: $("#carteras").val(),
        area_trabajo: $("#area_trabajo").val(),
    };

    $.ajax({
        type: 'POST',
        url: url,
        data: data,
        dataType: 'json',
        success: function (data)
        {
            if (data === null) {
                var Toastr = main();
                Toastr.fire({
                    icon: 'error',
                    title: 'El cargo y skill seleccionado no tienen indicadores registrados',
                });
            }
            $('#Tipoperiodo').html('');
            $('#Tipoperiodo').html(data[0]);

            $('label[for="fechaeval"]').html(data[0] !== null ? 'Fecha de Evaluacion ' + data[0].toUpperCase() + '<span style="color:red">*</span>' : 'Fecha de Evaluacion <span style="color:red">*</span>');

            Activatedatapicker(data[0],data);
        }
    });
});

//agrega fila de campos dinamicos al crear evaluacion
$('#add').click(function () {

    var cantidad, suma, form;
    cantidad = parseInt($(this).data('conteo'));
    suma = cantidad + 1;

    var contentToMove = $("#copycontent .row").clone();
    contentToMove.find('label[for="label1"]').text('Indicador');
    contentToMove.find('label[for="label1"]').attr('for', 'i' + suma); // Actualizar el atributo 'for' de las etiquetas        
    contentToMove.find('input[id="label1"]').attr('id', 'i' + suma); // Actualizar el atributo 'id' de los inputs
    contentToMove.find('input[name="label1"]').attr('name', 'i' + suma); // Actualizar el atributo 'name' de los inputs 

    contentToMove.find('label[for="label2"]').text('Valor de la Meta');
    contentToMove.find('label[for="label2"]').attr('for', 'm' + suma);
    contentToMove.find('input[id="label2"]').attr('id', 'm' + suma);
    contentToMove.find('input[name="label2"]').attr('name', 'm' + suma);

    contentToMove.find('select[id="label3"]').attr('id', 't' + suma);
    contentToMove.find('select[name="label3"]').attr('name', 't' + suma);

    $('#t' + suma).select2({
        theme: 'bootstrap4',
        placeholder: "[SELECCIONE]"
    });

    contentToMove.find('input[name="nuevo"]').attr('name', 'nuevo' + suma);
    contentToMove.find('input[id="nuevo"]').attr('id', 'nuevo' + suma);

    contentToMove.find('input[id="i' + suma + '"]').addClass('conteoinputs');
    contentToMove.find('button[id="deleted"]').attr('onclick', 'Rowdeleted(' + suma + ')');
    contentToMove.attr('id', 'rowinputs' + suma);
//console.log(contentToMove.find('button[id="deleted"]'));
    $('#pastecontent').append(contentToMove); // Agregar el contenido clonado al nuevo div
    $(this).data('conteo', suma);
    $('#nuevo' + suma).val(true);
    $('#totalindicadores').val(suma);

    console.log("ejecuta");
    $('#sendform').show();
});

//elimina la fila de campos dinamicos automaticos al crear la evaluacion
function Rowdeleted(value) {
    $('#rowinputs' + value).remove();
    var total = $('#totalindicadores').val();
}

///formularios de validacion generales para llamar.
var Validacion = function () {
    var Create = function () {
        $('#indicadores_store').validate({
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
                var selectedOptionText = $('#cargo option:selected').text();
                var selectskill = $('#skill option:selected').text();
                var conteoinputs = $('.conteoinputs').length;
                var metodo_eval = $('#tipo_periodo option:selected').text();
                Swal.fire({
                    title: "CONFIRMACION",
                    icon: "question",
                    html: "Se asignaran al Skill <b>" + selectskill + "</b> y al cargo de <b>" + selectedOptionText + "</b> la cantidad de " + conteoinputs + " indicador(es), su metodo de evaluación será <b>" + metodo_eval + "</b> ¿Esta de acuerdo con la información?",
                    showDenyButton: false,
                    showCancelButton: true,
                    confirmButtonText: "Procesar",
                    denyButtonText: "Regresar",
                    cancelButtonText: "Cancelar"
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {

                        form.submit();
                    }
                });
            }
        });
    };
    var Update = function () {
        $('#indicadores_update').validate({
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
                Swal.fire({
                    title: "CONFIRMACION",
                    icon: "question",
                    html: "Se procedera a actualizar la evaluación correspondiente al cargo de <b>" + $('#nombrecargo').val() + "<b style='color:red;'>ATENCION</b>: Al eliminar un de los indicadores existentes, se eliminaran las evaluaciones almacenadas. <b>¿Esta de acuerdo con la información?</b>",
                    showDenyButton: false,
                    showCancelButton: true,
                    confirmButtonText: "Procesar",
                    denyButtonText: "Regresar",
                    cancelButtonText: "Cancelar"
                }).then((result) => {
                    /* Read more about isConfirmed, isDenied below */
                    if (result.isConfirmed) {

                        form.submit();
                    }
                });
            }
        });
    };

    return {
        // public functions
        CreateEvaluacion: function () {
            Create();
        },
        UpdateEvaluacion: function () {
            Update();
        }
    };
}();

function DeleteSegmento(value) {
    event.preventDefault();
    Swal.fire({
        title: "CONFIRMACION",
        icon: "question",
        html: "Esta Seguro en eliminar los indicadores asignados al Cargo de <b>" + $('#deleteform' + value).data('cargo') + "</b>",
        showDenyButton: false,
        showCancelButton: true,
        confirmButtonText: "Procesar",
        denyButtonText: "Regresar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            $('#deleteform' + value).submit();
        }
    });
}

/// para el apartado de edicion y creacion de evaluaciones
// se llama al cambiar el cargo o la fecha
function Activatedatapicker(periodo,data) {
    switch (periodo) {
        case "semanal":
            $('#fechaeval').datetimepicker('destroy');
            $('#fechaeval').datetimepicker({
                format: 'MM/YYYY',
                minDate: moment().add(-5, 'months'),
                maxDate: moment(),
            });
            $('#periodo').empty();
            $('#periodo').attr('required', true);
            $('#periodo').append($("<option></option>").attr("value", '').text("[Seleccione Semanal]"));
            $.each(data[1], function (index, value) {               
                // Verificar que el valor no esté vacío
                if (value) {
                    var optionIndex = parseFloat(index)+1;                   
                    $('#periodo').append($('<option>', {
                        value: optionIndex, // Usa el índice como valor
                        text: value   // El texto de la opción
                    }));
                }
            });

            $('#periodo').attr('required', true);
            $('#appendperiodo').show();
            break;
        case"mensual":
            $('#periodo').empty();
            $('#periodo').attr('required', false);
            $('#appendperiodo').hide();

            $('#fechaeval').datetimepicker('destroy');
            $('#fechaeval').datetimepicker({
                format: 'MM/YYYY',
                minDate: moment().add(-5, 'months'),
                maxDate: moment(),
            });
            break;
        case"diario":
            $('#periodo').empty();
            $('#periodo').attr('required', false);
            $('#appendperiodo').hide();

            $('#fechaeval').datetimepicker('destroy');
            $('#fechaeval').datetimepicker({
                minDate: moment().subtract(7, 'days'),
                maxDate: moment(),
                format: 'DD/MM/YYYY',
            });

            break;
        case "quincenal":
            $('#periodo').empty();
            $('#periodo').attr('required', false);
            $('#periodo').append($("<option></option>").attr("value", '').text("[CORTE]"));

            $('#fechaeval').datetimepicker('destroy');
            $('#fechaeval').datetimepicker({
                format: 'MM/YYYY',
                minDate: moment().add(-5, 'months'),
                maxDate: moment(),
            });


            var arrayQuincenas = {"1": "1ra. Quincena", "2": "2da. Quincena"};
            $.each(arrayQuincenas, function (index, value) {
                // Verificar que el valor no esté vacío
                if (value) {
                    var optionIndex = index;
                    $('#periodo').append($('<option>', {
                        value: optionIndex, // Usa el índice como valor
                        text: value   // El texto de la opción
                    }));
                }
            });
            $('#appendperiodo').show();
            break;
        default:
            $('#periodo').empty();
            $('#periodo').attr('required', false);
            $('#appendperiodo').hide();
            $('#fechaeval').datetimepicker('destroy');
            $('#fechaeval').datetimepicker({
                format: 'MM/YYYY',
                minDate: moment().add(-5, 'months'),
                maxDate: moment(),
            });
            break;
    }
}

function ChangePeriodoActive(value) {
    var input, url, data, state;
    input = $('#segmentoactive' + value);
    if (input.bootstrapSwitch('state') === true) {
        url = input.data("href");
        state = 1;
    } else {
        url = input.data("href");
        state = 0;
    }
    data = {_token: $("input[name=_token]").val(), state: state}
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
                    icon: "success",
                    title: "&nbsp;" + data[0],
                });
            });
        }, error: function (data) {
            var Toastr = main();
            $(function () {
                Toastr.fire({
                    icon: "error",
                    title: "&nbsp;" + data.responseJSON.message,
                });
            });
        }
    });
}

function ShowEvaluacionEdit(evaluacion) {

    var boton = $('#button' + evaluacion);
    $('#editnombre_personal').val(boton.data('nombre'));
    $('#mesnaio').val(boton.data('mes_anio'));
    var data = {
        _token: $("input[name=_token]").val()
    };
    $.ajax({
        type: "POST",
        url: boton.data('href'),
        data: data,
        dataType: 'json',
        success: function (data) {

            $('#editeval').modal('show');
            $('#datarespuestas').empty();
            $('#datarespuestas').append(data);

        }, error: function (data) {

            $('#editeval').modal('hide');
            var Toastr = main();
            $(function () {
                Toastr.fire({
                    icon: "error",
                    title: "&nbsp;" + data.responseJSON.message,
                });
            });
        }
    })
}

function generar(){
    
    var area_trabajo = $('#area_trabajo').val();
    var carteras = $('#carteras').val();
    var skill_asig = $('#skill').val();
    var cargo_eval = $('#cargo').val();

    var button = $('#generar_formato');
    var url = button.data('href');

    if(area_trabajo == "" || carteras == "" || skill_asig == "" || cargo_eval == ""){
        var Toastr = main();
        Toastr.fire({
            icon: 'error',
            title: 'Por favor llene los campos faltantes',
        });
    } else {       
        var data = {area_trabajo: area_trabajo,
            carteras: carteras,
            skill_asig: skill_asig,
            cargo_eval: cargo_eval,
            _token: $("input[name=_token]").val()
        }
        $.ajax({
            url: url,
            method: "POST",
            data: data,
            xhrFields: {
                responseType: 'blob' // Esto es importante para descargar archivos
            },
            success: function (data)
            {
                var Toastr = main();
                Toastr.fire({
                    icon: 'success',
                    title: 'Formato generado exitosamente.',
                });

                var blob = new Blob([data]);
                var link = document.createElement('a');
                link.href = window.URL.createObjectURL(blob);
                link.download = 'FormatoIndicadores.xlsx'; // Nombre del archivo de descarga
                link.click();

            }, error: function (data) {
                var Toastr = main();
                Toastr.fire({
                    icon: 'error',
                    title: 'Ha ocurrido un error al generar el formato',
                });
            }
        });

    }

}

