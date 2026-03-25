$(function () {

    $('.input-number').on('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    if ($('.custom-file-input').length != 0) {
        $(function () {
            bsCustomFileInput.init();
        });
    }

    $(".allactionbuttons").on('click', function () {
        var action = $(this).val();
        $('.countaction').each(function () {
            console.log($(this));
            $(this).val(action).prop('selected', true).trigger('change');
        })
    });

    /// solo mes y año
    if ($('.mesdate').length !== 0) {
        $('.mesdate').datetimepicker({
            format: 'MM/YYYY',
            maxDate: moment(),
        });
    }
    /// rango de fechas
    if ($('.daterange').length !== 0) {
        $('.daterange').daterangepicker({
            locale: {
                format: 'DD/MM/YYYY',
            }
        });
    }

    if ($('.select2').length != 0) {
        console.log("select2");
        $('.select2').select2({ theme: 'bootstrap4' });
    }

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
    }

    if ($('#datatable-participantes').length != 0) {
        $('.table').DataTable().destroy();
        $("#datatable-participantes").DataTable({
            "lengthMenu": [10, 20, 15, 30, 50, 60],
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "ordering": true,
            "order": [0, "DESC"]
            //"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        });
    }

    if ($("input[type=checkbox]").length != 0) {
        $("input[type=checkbox]").bootstrapSwitch({ offText: "Off", onText: "On" });
    }

    if ($("form[id='editasistencia']").length != 0) {
        console.log("activo");
        motivos.init();
    }
});

/// buscar cursos -panel principal
$('#searchcursos').validate({
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

                if ($.fn.dataTable.isDataTable('#datatable-cursos')) {
                    $('#tablelistado').fadeOut();
                    $('#datatable-cursos').DataTable().destroy();
                }
                $('#datatable-cursos').DataTable({
                    "initComplete": function (settings, json) {
                        $('#tablelistado').fadeIn();
                    },
                    responsive: true,
                    "ordering": false,
                    "autoWidth": false,
                    "data": data,
                    dataSrc: "",
                    "lengthMenu": [10, 20, 50, 75],
                    "bPaginate": true,
                    "bProcessing": true,
                    "columns": [
                        { data: 'cod' },
                        { data: 'inicio' },
                        { data: 'finaliza' },
                        { data: 'requeridos' },
                        { data: 'skill' },
                        { data: 'facilitador' },
                        { data: 'sede' },
                        { data: 'estatus' },
                        { data: 'acciones' },
                    ]
                });
                ///aplica a elementos visibles
                $('.estatuscursos').bootstrapSwitch({ offText: "Off", onText: "On" });
                ///aplica a elementos de paginacion
                $('#datatable-cursos').on('draw.dt', function () {
                    $('.estatuscursos').bootstrapSwitch({ offText: "Off", onText: "On" });
                });
                ///aplica a elementos ocultos del responsive
                $('#datatable-cursos').on('responsive-display.dt', function (e, datatable, row, showHide) {
                    if (showHide) { // Si se muestra la fila "child"
                        $(row.node()).find('.estatuscursos').bootstrapSwitch({ offText: "Off", onText: "On" });
                    }
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

// aplicable ->create cursos
$('#sede').on('change', function () {
    console.log("ejecuta");
    var sede = $(this).val();
    if (sede == 'STV' || sede == 'ATENAS') {
        $(".region-estado").show();
        $('#region').prop('required', true);
        $('#estado').prop('required', true);
    } else {
        $(".region-estado").hide();
        $('#region').prop('required', false);
        $('#estado').prop('required', false);
    }
});
// aplicable ->create cursos
$('#region').on('change', function () {
    var region = $(this).val();
    var url = $(this).data('href');
    var _token = $("input[name='_token']").val();
    $.ajax({
        type: 'POST',
        url: url,
        data: { region: region, _token: _token },
        dataType: 'json',
        success: function (data) {
            $('#estado').empty();
            $('#estado').append($("<option></option>").attr("value", '').text("[SELECCIONE]"));
            for (i = 0; i < data.length; i++) {
                $('#estado').append($("<option></option>").attr("value", data[i].id).text(data[i].nombre_estado));
            }
        }
    });
});

$('#estado').on('change', function () {
    var estado_id = $(this).val();
    var url = $(this).data('href');
    var _token = $("input[name='_token']").val();
    $.ajax({
        type: 'POST',
        url: url,
        data: { estado_id: estado_id, _token: _token },
        dataType: 'json',
        success: function (data) {
            $('#ciudad').empty();
            $('#ciudad').append($("<option></option>").attr("value", '').text("[SELECCIONE]"));
            for (i = 0; i < data.length; i++) {
                $('#ciudad').append($("<option></option>").attr("value", data[i].id).text(data[i].ciudad));
            }
        }
    });
});
/// registrar cursos
$('#cursos_store').validate({
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

///asignar participantes y Edicion
$('#create-participantes').validate({
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

///buscar participantes por cursos
$('#SearchParticipantescursos').validate({
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
                if ($.fn.dataTable.isDataTable('#datatableParticipantesCursos')) {
                    $('#tablelistado').fadeOut();
                    $('#datatableParticipantesCursos').DataTable().destroy();
                }
                $('#datatableParticipantesCursos').DataTable({
                    "initComplete": function (settings, json) {
                        $('#tablelistado').fadeIn();
                    },
                    responsive: true,
                    "ordering": false,
                    "autoWidth": false,
                    "data": data,
                    dataSrc: "",
                    "lengthMenu": [10, 20, 50, 75],
                    "bPaginate": true,
                    "bProcessing": true,
                    "columns": [
                        { data: 'id' },
                        { data: 'inicio' },
                        { data: 'finaliza' },
                        { data: 'totalpartic' },
                        { data: 'cartera' },
                        { data: 'skill' },
                        { data: 'facilitador' },
                        { data: 'sede' },
                        { data: 'acciones' },
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

$('#carteras').on('change', function () {
    var cartera = $(this).val();
    var url = $(this).data('href');
    var _token = $("input[name='_token']").val();
    $.ajax({
        type: 'POST',
        url: url,
        data: { cartera: cartera, _token: _token },
        dataType: 'json',
        success: function (data) {
            $('#skill').empty();
            $('#skill').append($("<option></option>").attr("value", '').text("[SELECCIONE]"));
            for (i = 0; i < data.length; i++) {
                $('#skill').append($("<option></option>").attr("value", data[i].id).text(data[i].nombre_skill));
            }
        }
    });
});

$('#cargofac').on('change', function () {
    var cargo = $(this).val();
    var url = $(this).data('href');
    var _token = $("input[name='_token']").val();
    $.ajax({
        type: 'POST',
        url: url,
        data: { idCargo: cargo, _token: _token },
        dataType: 'json',
        success: function (data) {
            $('#facilitador').empty();
            $('#facilitador').append($("<option></option>").attr("value", '').text("[SELECCIONE]"));
            for (i = 0; i < data.length; i++) {
                $('#facilitador').append($("<option></option>").attr("value", data[i].id).text(data[i].primer_nombre + " " + data[i].primer_apellido));
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

/////modificar estatus de los cursos
$(document).on('switchChange.bootstrapSwitch', '.estatuscursos', function (event, state) {

    // Determinamos que switch cambió y actuamos el modal
    //$('#' + $(this).prop('id')).bootstrapSwitch('state', !state, true);
    switch ($('#' + $(this).prop('id')).bootstrapSwitch('state')) {
        case true:
            var data = { estatus: 1, _token: $("input[name='_token']").val() };
            var confirmacion = '¿Está de acuerdo en ejecutar esta acción? Una vez activado, no se podrá modificar sus datos, solo podrá Desactivado.';
            break;
        case false:
            var data = { estatus: 2, _token: $("input[name='_token']").val() };
            var confirmacion = '¿Está de acuerdo en ejecutar esta acción? Una vez Desactivado, el mismo no estara disponible y se cerrara automaticamente.'
            break;
    }

    Swal.fire({
        title: '¡Atención!',
        text: confirmacion,
        icon: 'info',
        showCancelButton: true,
        showConfirmButton: true,
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Confirmar',
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'PUT',
                url: $('#' + $(this).prop('id')).data('href'),
                data: data,
                dataType: 'json',
                success: function (data) {

                    var Toastr = main();
                    Toastr.fire({
                        icon: 'success',
                        title: data.msg,
                    });
                }, error: function (data) {
                    var Toastr = main();
                    Toastr.fire({
                        icon: 'error',
                        title: " " + data.responseJSON.message,
                    });
                }
            });
        }
    });
});

///elimiar los cursos
function DeleteCursos(value) {
    /// activamos el modal y en funcion al modal activado enviamos las propiedades del id del curso url y tipo de datos al modal.

    Swal.fire({
        title: '¡Atención!',
        text: '¿Esta de acuerdo en ejecutar esta Acción? Tenga en cuenta que debera volver a realizar el proceso de asignación de participantes si ya estaban asignados a este curso"',
        icon: 'info',
        showCancelButton: true,
        showConfirmButton: true,
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Confirmar',
    }).then((result) => {     // Se agrega el método .then()
        if (result.isConfirmed) {    // Aquí se valida si se presionó 'Cancelar'
            $('#deleted' + value).submit();  // Se limpia el input 'fegreso'
        }
    });
}

//buscar participantes por cedula o nombre
$('#ParticipantesBuscador').validate({
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
                if ($.fn.dataTable.isDataTable('#datatableParticipantesCursos')) {
                    $('#tablelistado').fadeOut();
                    $('#datatableParticipantesCursos').DataTable().destroy();
                }
                $('#datatableParticipantesCursos').DataTable({
                    "initComplete": function (settings, json) {
                        $('#tablelistado').fadeIn();
                    },
                    responsive: true,
                    "ordering": false,
                    "autoWidth": false,
                    "data": data,
                    dataSrc: "",
                    "lengthMenu": [10, 20, 50, 75],
                    "bPaginate": true,
                    "bProcessing": true,
                    "columns": [
                        { data: 'cedula' },
                        { data: 'nombre' },
                        { data: 'telefono' },
                        { data: 'curso' },
                        { data: 'estatus' },
                        { data: 'acciones' },
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

function DeleteParticipantes(value) {
    /// activamos el modal y en funcion al modal activado enviamos las propiedades del id del curso url y tipo de datos al modal.

    Swal.fire({
        title: '¡Atención!',
        text: '¿Esta de acuerdo en ejecutar esta Acción?Sera eliminado toda la informacion del participante y de sus cursos anteriores.',
        icon: 'info',
        showCancelButton: true,
        showConfirmButton: true,
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Confirmar',
    }).then((result) => {     // Se agrega el método .then()
        if (result.isConfirmed) {    // Aquí se valida si se presionó 'Cancelar'
            $('#deleted' + value).submit();  // Se limpia el input 'fegreso'
        }
    });
}

function DisplayHistorial(value) {

    $('#viewhistory').modal('show');
    var url, data;
    url = $('#historial' + value).data('href');
    console.log(url);
    data = { _token: $("input[name='_token']").val() };

    $.ajax({
        type: 'POST',
        url: url,
        data: data,
        dataType: 'json',
        success: function (data) {
            if ($.fn.dataTable.isDataTable('#datatable-historial')) {
                $('#datatable-historial').DataTable().destroy();
            }
            $('#datatable-historial').DataTable({
                "initComplete": function (settings, json) {
                    $('#tablelistado').fadeIn();
                },
                responsive: true,
                "ordering": false,
                "autoWidth": false,
                "data": data,
                dataSrc: "",
                "lengthMenu": [10, 20, 50, 75],
                "bPaginate": true,
                "bProcessing": true,
                "columns": [
                    { data: 'curso' },
                    { data: 'fecha_inicio' },
                    { data: 'fecha_fin' },
                    { data: 'estatus' }
                ]
            });
        }, error: function (data) {
            var Toastr = main();
            Toastr.fire({
                icon: 'error',
                title: "&nbsp; " + data.responseJSON.message,
            });
        }
    });
}

/////Permitira cambiar el calendario segun el curso seleccionado
$('#cursoasistencia').on('change', function () {

    if ($('.daterangedialy').data('DateTimePicker')) {
        console.log("existe un date");
        // Destruir el datetimepicker existente
        $('.daterangedialy').datetimepicker('destroy');
        $('.daterangedialy').val(null); // Opcional: limpiar input al destruir
    }

    var inicio = $('select[name="cursoasistencia"] option:selected').data('inicio');
    var fin = $('select[name="cursoasistencia"] option:selected').data('fin');

    if (inicio === undefined || fin === undefined) {
        $('.daterangedialy').attr('disabled', true);
    } else {
        $('.daterangedialy').datetimepicker({
            format: 'DD/MM/YYYY',
            minDate: moment(inicio, 'YYYY-MM-DD'), // Ajusta el formato según cómo venga la fecha
            maxDate: moment(fin, 'YYYY-MM-DD'),
        });
        $('.daterangedialy').attr('placeholder', 'Seleccione Fecha');
        $('.daterangedialy').removeAttr('disabled');
    }
});
/////Buscar asistencia del curso
$('#asistenciasearchcursos').validate({
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
        var url, data;
        url = form.action + "/" + $('#cursoasistencia').val();
        data = { _token: $("input[name='_token']").val(), fecha: $('#fecha').val() };

        $.ajax({
            type: 'POST',
            url: url,
            data: data,
            dataType: 'html',
            success: function (data) {
                $('#display_asistencia').empty();
                $('#display_asistencia').append(data);
                $('.select2').select2({ theme: 'bootstrap4' });

                motivos.init();
                validation.init();
            }, error: function (data) {
                console.log(data.responseText);
                var Toastr = main();
                Toastr.fire({
                    icon: 'error',
                    title: "&nbsp; " + data.responseText,
                });
            }
        });
    }
});
///seleccion de motivos de egreso y abandono del formulario de asistencia.
var motivos = function () {

    var MotivosEgresos = function () {
        $('.inputasistencia').change(function () {
            $(this).each(function () {
                var input, campoId, seleccion, valorcampomotivo;

                input = $(this); /// campo que ha cambiado de valor
                valorcampomotivo = input.data('valor');

                if ($.inArray(input.val(), ["E", "AB"]) !== -1) {
                    $("#display" + valorcampomotivo).show();
                    seleccion = input.val() === "E" ? egresos : abandonos;
                    $('#motivo' + valorcampomotivo).attr('required', 'required');
                    $('#motivo' + valorcampomotivo).empty();
                    $('#motivo' + valorcampomotivo).append($("<option></option>").attr("value", '').text("[SELECCIONE]"));
                    for (i = 0; i < seleccion.length; i++) {
                        $('#motivo' + valorcampomotivo).append($("<option></option>").attr("value", seleccion[i].id).text(seleccion[i].observaciones));
                    }
                } else {
                    ///en caso que el selector regrese a otro valor diferente a egresos y abandonos
                    $("#display" + valorcampomotivo).hide();
                    $('#motivo' + valorcampomotivo).empty();
                    $('#motivo' + valorcampomotivo).append($("<option></option>").attr("value", '0').text("N/A"));
                    $('#motivo' + valorcampomotivo).removeAttr('required', 'required');
                }


            });
        });
    };
    return {
        init: function () {
            MotivosEgresos();
        }
    };
}();
///validacion de formulario de asistencia
var validation = function () {

    var asistenciaCursos = function () {
        $('#asistenciastore').validate({
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

                console.log("validacion asistencia", $('#fincurso').val() === $('#fechas').val());
                if ($('#fincurso').val() === $('#fechaasistencia').val()) {

                    Swal.fire({
                        title: '¡Atención!',
                        html: 'Esta a punto de registrar la asistencia correspondiente al ultimo día del curso. Esta Acción cerrara el curso para que puedas Aprobar a los participantes. <b>Recuerda que antes de continuar, debes registrar los días anteriores.</b>',
                        icon: 'info',
                        showCancelButton: true,
                        showConfirmButton: true,
                        cancelButtonText: 'Cancelar',
                        confirmButtonText: 'Confirmar',
                    }).then((result) => {     // Se agrega el método .then()
                        if (result.isConfirmed) {    // Aquí se valida si se presionó 'Cancelar'
                            form.submit();
                        }
                    });
                } else {
                    form.submit();
                }
            }
        });
    };
    return {
        init: function () {
            asistenciaCursos();
        }
    };
}();
///validacion de formulario de asistencia
var ValidacionAprobaciones = function () {

    var AprobacionValidate = function () {
        $('#formaprobacionparticipantes').validate({
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
                $(element).addClass('is-valid');
            },
            submitHandler: function (form) {
                console.log($('#fincurso').val(), $('#fechas').val());
                conteomigrado = 0;
                conteototal = $('.countaction').length;
                $('.countaction').each(function () {
                    if ($(this).val() === "1") {
                        conteomigrado++;
                    }
                });
                console.log(conteomigrado, conteototal);

                if ($('#typevalidador').val() === "aprobar") {
                    var confirm = 'El participante que tenga un estatus de "Esperando Certificación",se mantendra disponible \n\
                        Hasta 15 dias posterior al cierre del curso. Los Estatus no podran ser modificados una vez sea Migrado El curso\n\
                        <b>¿Esta de acuerdo?</b>';
                } else {
                    var confirm = 'Se procede a cargar el expediente de <b>' + conteomigrado + '</b> Participantes de un total de <b>' + conteototal + '</b>, Los registros quedaran en espera Hasta un Maximo de 15 Días.<br> Recuerde actualizar el Expediente del personal Migrado. <br/><b>¿Esta de acuerdo?</b>';
                }

                Swal.fire({
                    title: '¡Atención!',
                    html: confirm,
                    icon: 'info',
                    showCancelButton: true,
                    showConfirmButton: true,
                    cancelButtonText: 'Cancelar',
                    confirmButtonText: 'Confirmar',
                }).then((result) => {     // Se agrega el método .then()
                    if (result.isConfirmed) {    // Aquí se valida si se presionó 'Cancelar'
                        form.submit();
                    }
                });
            }
        });
    };
    return {
        init: function () {
            AprobacionValidate();
        }
    };
}();
///buscar asistencias por cursos para modificar
$('#editasistenciaforcursos').validate({
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
                if ($.fn.dataTable.isDataTable('#datatable-asistencias')) {
                    $('#tablelistado').fadeOut();
                    $('#datatable-asistencias').DataTable().destroy();
                }
                $('#datatable-asistencias').DataTable({
                    "initComplete": function (settings, json) {
                        $('#tablelistado').fadeIn();
                    },
                    responsive: true,
                    "ordering": false,
                    "autoWidth": false,
                    "data": data,
                    dataSrc: "",
                    "lengthMenu": [10, 20, 50, 75],
                    "bPaginate": true,
                    "bProcessing": true,
                    "columns": [
                        { mData: 'cod' },
                        { mData: 'cedula' },
                        { mData: 'nombre' },
                        { mData: 'skill' },
                        { mData: 'prefijo' },
                        { mData: 'observacion' },
                        { mData: 'acciones' }
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

//// buscar cursos para aprobar
$('#aprobarparticipantes').validate({
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
        var url = form.action + "/" + $('#cursoasistencia').val();
        $.ajax({
            type: 'POST',
            url: url,
            data: $(form).serialize(),
            dataType: 'html',
            success: function (data) {
                $('#displayaprobacion').empty();
                $('#displayaprobacion').append(data);
                $('#acctionsbuttons').show();
                $('.select2').select2({ theme: 'bootstrap4' });
                $('#datatable-aprobacion').DataTable({
                    "lengthMenu": [20, 15, 30, 50, 60],
                    "responsive": true,
                    "lengthChange": true,
                    "autoWidth": false,
                    "ordering": true,
                });

                if ($('.countaction').length === 0) {
                    $('#sendmigracion').attr('disabled', 'disabled');
                }
                ValidacionAprobaciones.init();

            }, error: function (data) {
                var Toastr = main();
                Toastr.fire({
                    icon: 'error',
                    title: "&nbsp; " + data.responseJSON.message,
                });
            }
        });
    }
});

function AprobacionesObservacionesChange(val) {

    var url = $('#urlobservaciones').val();
    var data = {
        estatus: $('#listado' + val).val(), _token: $("input[name='_token']").val()
    }
    $.ajax({
        type: 'POST',
        url: url,
        data: data,
        dataType: 'json',
        success: function (data) {
            $('#observacion' + val).empty();
            $('#observacion' + val).append($("<option></option>").attr("value", '').text("[SELECCIONE]"));
            for (i = 0; i < data.length; i++) {
                $('#observacion' + val).append($("<option></option>").attr("value", data[i].id).text(data[i].observaciones));
            }
        }, error: function (data) {
            var Toastr = main();
            Toastr.fire({
                icon: 'error',
                title: "&nbsp; " + data.responseJSON.message,
            });
        }
    });
}


function ChangeAccion(val) {
    if ($('#accion' + val).val() === "1") {
        $('#documentacion' + val).attr('required', 'required');
        $('#listado' + val).attr('required', 'required');
    } else {
        $('#documentacion' + val).removeAttr('required', 'required');
        $('#listado' + val).removeAttr('required', 'required');
    }
}


function checkAsistencia() {
    if ($('.inputasistencia').length === 0) {
        var Toastr = main();
        Toastr.fire({
            icon: 'info',
            title: "&nbsp; Debes ubicar primero el personal a quien se le tomara asistencia",
        });
    } else {
        $('.inputasistencia').val('A').trigger('change');
        //// si se selecciona el check de asistentes todos los motivos pasaran a 0
        if ($(".motivos").is(":visible")) {
            $(".motivos").hide();
            $(".selmotivos").removeAttr('required', 'required');
        }
    }
}
