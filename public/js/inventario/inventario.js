function matchCustom(params, data) {
    // If there are no search terms, return all of the data
    if ($.trim(params.term) === '') {
        return data;
    }

    // Do not display the item if there is no 'text' property
    if (typeof data.text === 'undefined') {
        return null;
    }

    // `params.term` should be the term that is used for searching
    // `data.text` is the text that is displayed for the data object
    if (data.text.indexOf(params.term) > -1) {
        var modifiedData = $.extend({}, data, true);
        modifiedData.text += ' (matched)';

        // You can return modified objects from here
        // This includes matching the `children` how you want in nested data sets
        return modifiedData;
    }

    // Return `null` if the term should not be displayed
    return null;
}

$.validator.addMethod("selectMultipleRequired", function (value, element) {
    $(element).closest('.select2').addClass('is-invalid');
    return $(element).val() !== null && $(element).val().length > 0;
});
$.validator.addMethod("alMenosUnoSeleccionado", function (value, element) {
    return $('.check:checked').length > 0;
}, "Seleccione al menos un elemento.");

$(function () {
    if ($(".select2").length != 0) {
        $('.select2').select2({
            theme: 'bootstrap4',
            'responsive': true,
            'placeholder': "[Seleccione una o mas Opciones]",
        });
    }

    if ($(".table").length != 0) {
        $('.table').DataTable({
            "lengthMenu": [10, 20, 15, 30, 50, 60],
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "ordering": true,
            //"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        });
    }
    /// cambio del swich
    if ($(".swichBoostrap").length != 0) {
        $(".swichBoostrap").bootstrapSwitch({
            offText: "BAD",
            onText: "GOOD"
        });
    }

    if ($(".swichBoostrapOn").length != 0) {
        $(".swichBoostrapOn").bootstrapSwitch({
            offText: "OFF",
            onText: "ON"
        });
    }

    if ($(".daterangedialy").length != 0) {
        $('.daterangedialy').datetimepicker({
            format: 'L',
            format: 'DD/MM/YYYY',

        });
    }

    if ($(".duallistbox").length != 0) {
        $('.duallistbox').bootstrapDualListbox({
            nonSelectedListLabel: 'Non-selected',
            selectedListLabel: 'Selected',
            preserveSelectionOnMove: 'moved',
            moveOnSelect: false,
        });
    }

    $("#select_all").on('change', function () {
        $(".check").prop('checked', $(this).prop("checked"));
    });

});

///creation de componentes
$("#componente").change(function () {
    if ($(this).val() === "CPU") {
        $('#viewgeneracion').fadeIn();
        $('#generacion').attr('required', 'required');
    } else {
        $('#viewgeneracion').fadeOut();
        $('#generacion').removeAttr('required', 'required');
    }
});

///registrar componente
$("#stock_create").validate({
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
        $(':input[type="submit"]').prop('disabled', true);
        form.submit();
    }
});
/// buscar componentes
$("#IndexComponentSearch").validate({
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

                if ($.fn.dataTable.isDataTable('#datatable-inventario')) {
                    $('#tablelistado').fadeOut();
                    $('#datatable-inventario').DataTable().destroy();
                }

                $('#datatable-inventario').DataTable({
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
                        { mData: 'componente' },
                        { mData: 'tipo' },
                        { mData: 'capacidad' },
                        { mData: 'available' },
                        { mData: 'asignado' },
                        { mData: 'bad' },
                        { mData: 'total' }
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

$("#periferico_create").validate({
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
        $(':input[type="submit"]').prop('disabled', true);
        form.submit();
    }
});

$("#IndexPerifericosSearch").validate({
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

                if ($.fn.dataTable.isDataTable('#datatable-inventario')) {
                    $('#tablelistado').fadeOut();
                    $('#datatable-inventario').DataTable().destroy();
                }

                $('#datatable-inventario').DataTable({
                    "initComplete": function (settings, json) {
                        $('#tablelistado').fadeIn();
                    },
                    responsive: true,
                    "ordering": false,
                    "autoWidth": false,
                    data: data[0],
                    "lengthMenu": [20, 50, 75],
                    "bPaginate": true,
                    "bProcessing": true,
                    "columns": [
                        { mData: 'tipo' },
                        { mData: 'conexion' },
                        { mData: 'available' },
                        { mData: 'asignado' },
                        { mData: 'bad' },
                        { mData: 'total' }
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

///registrar monitor
$("#monitor_create").validate({
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
        $(':input[type="submit"]').prop('disabled', true);
        form.submit();
    }
});

/// formateamos el campo de macadress del formulario
$('#macadress').on('input', function () {
    var valorCampo = $(this).val();

    // Eliminar caracteres no válidos
    var valorFormateado = valorCampo.replace(/[^0-9A-Fa-f]/g, '');

    // Limitar la cantidad de pares a 6
    valorFormateado = valorFormateado.slice(0, 12);

    // Insertar dos puntos después de cada par de caracteres
    valorFormateado = valorFormateado.replace(/(.{2})/g, '$1:');

    // Eliminar el último carácter si es un dos puntos
    if (valorFormateado.charAt(valorFormateado.length - 1) === ':') {
        valorFormateado = valorFormateado.slice(0, -1);
    }

    // Asignar el valor formateado al campo
    $(this).val(valorFormateado);
});

///par asignar componentes a los equipos
$("#asignasede").change(function () {

    var url, data, disks, incluirdiv, incluirrams, rams;

    incluirdiv = $('#incluirdiscos');
    incluirrams = $('#incluirrams');

    disks = parseInt($('#disks').val());
    rams = parseInt($('#assignedram').val());

    url = $(this).data('href');
    data = {
        almacen: $(this).val(),
        _token: $("input[name=_token]").val(),
    }

    $.ajax({
        url: url,
        method: "POST",
        data: data,
        dataType: "JSON",
        success: function (data) {
            if ($.fn.dataTable.isDataTable('#datatable-inventario')) {
                $('.tablelistado').fadeOut();
                $('#datatable-inventario').DataTable().destroy();
            }
            $('#datatable-inventario').DataTable({
                "initComplete": function (settings, json) {
                    $('.tablelistado').fadeIn();
                },
                "responsive": true,
                "ordering": false,
                "autoWidth": false,
                "data": data[0],
                "lengthMenu": [5, 10, 20, 50, 75],
                "bPaginate": true,
                "bProcessing": true,
                "columns": [
                    { mData: 'componente' },
                    { mData: 'tipo' },
                    { mData: 'capacidad' },
                    { mData: 'available' },
                    { mData: 'asignado' },
                    { mData: 'bad' },
                ]
            });
            $('#cpu').empty();
            $('#cpu').append($("<option></option>").attr("value", '').text("[SELECCIONE]"));
            for (c = 0; c < data[2].length; c++) {
                $('#cpu').append($("<option></option>").attr("value", data[2][c].id).text(data[2][c].texto));
            }
            $('#cpu').select2({ theme: 'bootstrap4' });

            incluirdiv.empty();
            for (cantidad = 1; cantidad <= disks; cantidad++) {
                inputstatusdisk = '<label class="col-form-label">Nuevo Disco ' + cantidad + '</label><span style="color:red">*</span>\n\
                <div class="input-group">\n\
                <select id="selectdisco' + cantidad + '"  name="selectdisco' + cantidad + '" required="required" class="form-control select2" style="width: 100%;">\n\
                </select></div>';
                incluirdiv.append(inputstatusdisk);

                $('#selectdisco' + cantidad).append($("<option></option>").attr("value", '').text("[SELECCIONE]"));
                for (d = 0; d < data[3].length; d++) {
                    $('#selectdisco' + cantidad).append($("<option></option>").attr("value", data[3][d].id).text(data[3][d].texto));
                }

                $('#selectdisco' + cantidad).select2({ theme: 'bootstrap4' });

                $('#selectdisco' + cantidad).on('select2:select', function (e) {
                    console.log("ejecuto remove discos");
                    var selectedOption = e.params.data.id;  // Obtener el valor seleccionado del select que ha cambiado
                    $('select.select2').not(this).find('option[value="' + selectedOption + '"]').remove();  // Eliminar la opción seleccionada del resto de selectores
                });
            }

            incluirrams.empty();
            for (cantidadram = 1; cantidadram <= rams; cantidadram++) {
                inputsrams = '<label class="col-form-label">Nueva RAM ' + cantidadram + '</label><span style="color:red">*</span>\n\
                <div class="input-group">\n\
                <select id="selectram' + cantidadram + '"  name="selectram' + cantidadram + '" required="required" class="form-control select2" style="width: 100%;">\n\
                </select></div>';

                incluirrams.append(inputsrams);
                $('#selectram' + cantidadram).append($("<option></option>").attr("value", '').text("[SELECCIONE]"));
                for (i = 0; i < data[1].length; i++) {
                    $('#selectram' + cantidadram).append($("<option></option>").attr("value", data[1][i].id).text(data[1][i].texto));
                }

                $('#selectram' + cantidadram).select2({ theme: 'bootstrap4' });

                $('#selectram' + cantidadram).on('select2:select', function (e) {
                    console.log("ejecuto remove rams");
                    var selectedOption = e.params.data.id;  // Obtener el valor seleccionado del select que ha cambiado
                    $('select.select2').not(this).find('option[value="' + selectedOption + '"]').remove();  // Eliminar la opción seleccionada del resto de selectores
                });
            }
            /*$('.select2').rules('add', {
             selectMultipleRequired: true
             });*/
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

$("#equipos_create").validate({
    ignore: [],
    lang: 'es',
    rules: {
        assignedram: {
            max: function () {
                return $("#slot").val(); // Asegúrate de que el ID sea correcto
            }
        }
    },
    errorElement: 'span',
    validClass: "is-valid",
    errorPlacement: function (error, element) {
        error.addClass('is-invalid text-sm');
        element.closest('.form-group').append(error);
    },
    highlight: function (element, errorClass, validClass) {
        $(element).addClass('is-invalid text-sm');

        $('.nav-tabs').find('small.required').remove();

        $('.tab-content .tab-pane:has(.is-invalid)').each(function () {
            var id = $(this).attr('id');
            $('.nav-tabs').find('a.nav-link[aria-controls=' + id + ']').append(' <small class="required" style="color:red;"> <b>***</b></small>');
        });
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass('error is-invalid text-sm');
    },
    submitHandler: function (form) {
        $(':input[type="submit"]').prop('disabled', true);
        form.submit();
    }
});

$("#IndexSearchEquip").validate({
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

        if (form.stock.value === '' && form.equipo.value === '') {
            var Toastr = main();
            Toastr.fire({
                icon: 'info',
                title: "&nbsp; Debe usar una opción de consulta"
            });
        } else if (form.stock.value !== '' && form.equipo.value !== '') {
            form.equipo.value = '';

            var Toastr = main();
            Toastr.fire({
                icon: 'info',
                title: "&nbsp; Debe usar una de las dos opciones de consulta",
            });
        } else {

            $.ajax({
                type: form.method,
                url: form.action,
                data: $(form).serialize(),
                dataType: 'json',
                success: function (data) {

                    if (data[2]) {
                        $('#tablelistado').fadeIn();
                        $("#mapsvg").empty();
                        $("#mapsvg").append(data[1]).fadeIn();
                        $('[data-bs-toggle="tooltip"]').tooltip();
                    } else {
                        $('#tablelistado').fadeIn();
                        $("#mapsvg").empty();
                    }

                    if ($.fn.dataTable.isDataTable('#datatable-equipos')) {
                        $('#tablelistado').fadeOut();
                        $('#datatable-equipos').DataTable().destroy();
                    }

                    $('#datatable-equipos').DataTable({
                        "initComplete": function (settings, json) {
                            $('#tablelistado').fadeIn();
                        },
                        responsive: true,
                        "ordering": false,
                        "autoWidth": false,
                        data: data[0],
                        "lengthMenu": [5, 20, 50, 75],
                        "bPaginate": true,
                        "bProcessing": true,
                        "columns": [
                            { mData: 'id' },
                            { mData: 'estatus' },
                            { mData: 'gerencia' },
                            { mData: 'cartera' },
                            { mData: 'skill' },
                            { mData: 'responsable' },
                            { mData: 'posicion' },
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
    }
});

///cambios de estatus de todos los Componentes en equipo
function ChangeEstatusComponentequip(input, value) {
    var data, url;

    if ($('#estatus' + value).bootstrapSwitch('state') === false) {
        $('#cambio' + value).removeAttr('disabled');
        $('#cambio' + value).val($('#cambio' + value + ' > option:first').val()).trigger('change');

        $('#stock' + value).val($('#stock' + value + ' > option:first').val());
        $('#stock' + value).attr('required');

        /// se valida que el campo que cambia no sea monitor, esto para que no limpie el selector en la vista de editar posicion
        input !== 'monitor' ? $('#new' + value).empty() : "";
        $('#new' + value).attr('required');

        $('#tipoestatus' + value + ' option[value=3]').attr("selected", true);
    } else {
        $('.display' + value).fadeOut();
        $('#cambio' + value).attr('disabled', 'disabled');
        $('#cambio' + value).val(2).trigger('change');

        /// se valida que el campo que cambia no sea monitor, esto para que no limpie el selector en la vista de editar posicion
        $('#stock' + value).val($('#stock' + value + ' > option:first').val()).trigger('change');
        input !== 'monitor' ? $('#new' + value).empty() : "";

        $('#tipoestatus' + value + ' option[value=1]').trigger('change').attr("selected", true);
    }
    url = $('#estatus' + value).data('href');

    data = {
        campo: input,
        _token: $("input[name=_token]").val(),
        estatus: $('#estatus' + value).bootstrapSwitch('state'),
    },
        $.ajax({
            url: url,
            dataType: 'json',
            type: "PUT",
            data: data,
            success: function (data) {
                var Toastr = main();
                Toastr.fire({
                    icon: 'success',
                    title: "&nbsp;" + data
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

$("#IndexPerifericosSearch").validate({
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

////////////// REMPLAZAR COMPOENTNES AL EQUIPO
function DisplayChangeRemplaceComponentes(type, value, form) {
    ///si el selector de Cambiar es SI, se ubican los inputs y se activan para su seleccion, 
    //en caso contrario si retiran los required
    if ($('#cambio' + value).val() === "1") {
        $('.display' + value).fadeIn();
        $('#stock' + value).attr('required', true);
        $('#new' + value).attr('required', true);

        $('#' + form).find('button[type="submit"]').each(function () {
            $(this).removeAttr('disabled', 'disabled');
        });
        RemplazoDeComponentesEquipos.init(form);
    } else {
        $('.display' + value).fadeOut();
        $('#stock' + value).removeAttr('required');
        $('#new' + value).removeAttr('required');
    }
}
///funcion para los selectores del stock para remplazar los componentes
function SelectStockRemplaceComponentes(type, val) {

    var input, data, url;

    if ($('#stock' + val).is(':visible') && $('#stock' + val).val() !== '') {
        input = $('#new' + val);
        data = {
            campo: type,
            _token: $("input[name=_token]").val(),
            stock: $('#stock' + val).val(),
        };
        url = type === "equipo" ? $('#rutaAsignarEquipos').val() : $('#rutaAsignarPerifericos').val();
        $.ajax({
            url: url,
            dataType: 'json',
            type: "POST",
            data: data,
            success: function (data) {
                input.empty();
                input.append($("<option></option>").attr("value", '').text("[SELECCIONE]"));
                console.log(data[1] !== [], data[1].length !== 0);
                if (data[1] !== []) {
                    for (i = 0; i < data[1].length; i++) {
                        input.append($("<option></option>").attr("value", data[1][i].id).text(data[1][i].descripcion));
                    }
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
}
///formulario para el remplazo de componentes del equipo
var RemplazoDeComponentesEquipos = function () {

    var ValidadorRemplazos = function (idFormulario) {

        $('#' + idFormulario).validate({
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

                $(form).find('input[disabled]').each(function () {
                    $(this).attr('readonly', true).removeAttr('disabled');
                });

                $(form).find('button[type="submit"]').each(function () {
                    $(this).attr('disabled', 'disabled');
                });

                $.ajax({
                    url: form.action,
                    dataType: 'json',
                    type: form.method,
                    data: $(form).serialize(),
                    success: function (data) {
                        var Toastr = main();
                        Toastr.fire({
                            icon: 'success',
                            title: "&nbsp;" + data + " Espere...",
                        });

                        setTimeout(function () {
                            window.location.reload();
                        }, 5000);

                    }, error: function (data) {
                        $(form).find('button[type="submit"]').each(function () {
                            $(this).removeAttr('disabled', 'disabled');
                        });
                        var Toastr = main();
                        Toastr.fire({
                            icon: 'error',
                            title: data.responseJSON.message,
                        });
                    }
                });
            }
        });
    };
    return {
        init: function (idFormulario) {
            ValidadorRemplazos(idFormulario);
        }
    };
}();

//////////////AGREGAR NUEVOS COMPONETES AL EQUIPO

$('.addcomponente').click(function () {
    $('#newcomponente').modal({
        backdrop: 'static',
        keyboard: false
    });
    $('#addcomponente').val($('#addcomponente > option:first').val());
    $('#addNewcomponentes').attr('action', $(this).data('href'));

    $("#selectNewComponente").data('type', $(this).data('type'));
    $("#selectNewComponente").attr('required', 'required');
    addNewComponente.init();
    $("#tablelistado").hide();
});

$("#addcomponente").change(function () {

    var data, url;
    url = $(this).data('href');
    data = {
        almacen: $(this).val(),
        _token: $("input[name=_token]").val(),
        type: $("#selectNewComponente").data('type'),
    };
    $.ajax({
        url: url,
        dataType: 'json',
        type: "post",
        data: data,
        success: function (data) {

            if ($.fn.dataTable.isDataTable('#datatable-inventario')) {
                $('#tablelistado').fadeOut();
                $('#datatable-inventario').DataTable().destroy();
            }

            $('#datatable-inventario').DataTable({
                "initComplete": function (settings, json) {
                    $('#tablelistado').fadeIn();
                },
                responsive: true,
                "ordering": false,
                "autoWidth": false,
                data: data[0],
                "lengthMenu": [10, 20, 50, 75],
                "bPaginate": true,
                "bProcessing": true,
                "columns": [
                    { mData: 'componente' },
                    { mData: 'tipo' },
                    { mData: 'capacidad' },
                    { mData: 'available' },
                    { mData: 'asignado' },
                    { mData: 'bad' },
                ]
            });

            $('#selectNewComponente').empty();
            $('#selectNewComponente').select2({ dropdownParent: $('#newcomponente .modal-body') });
            $('#selectNewComponente').append($("<option></option>").attr("value", '').text("[SELECCIONE]"));
            for (i = 0; i < data[1].length; i++) {
                $('#selectNewComponente').append($("<option></option>").attr("value", data[1][i].id).text(data[1][i].texto));
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

var addNewComponente = function () {

    var NeWComponet = function () {

        $('#addNewcomponentes').validate({
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
    };
    return {
        init: function () {
            NeWComponet();
        }
    };
}();

////ACTUALIZAR EL EQUIPO

$("#FromEditequipos").validate({
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
        // Deshabilitar el botón de envío
        $(':input[type="submit"]').prop('disabled', true);

        // Obtener datos del primer formulario
        var formData = $(form).serialize();

        // Obtener datos del segundo formulario
        var secondFormData = $("#FromUnlinkComponentes").serialize(); // Cambia el ID según tu formulario

        // Combinar los datos de ambos formularios
        var combinedData = formData + '&' + secondFormData;

        // Enviar los datos combinados
        $.ajax({
            url: form.action, // Asegúrate de que la URL sea correcta
            type: form.method,
            data: combinedData,
            success: function (response) {
                var Toastr = main();
                Toastr.fire({
                    icon: 'success',
                    title: response,
                });
            },
            error: function (xhr, status, error) {
                var Toastr = main();
                Toastr.fire({
                    icon: 'error',
                    title: status.responseJSON.message,
                });
            },
            complete: function () {
                // Habilitar el botón de envío nuevamente
                $(':input[type="submit"]').prop('disabled', false);
                setTimeout(function () {
                    window.location.reload();
                }, 3000);
            }
        });
    }
});

////POSICIONES BUSCAR
$("#IndexSearchPosiciones").validate({
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

        if (form.stock.value === '' && form.posicion.value === '') {
            var Toastr = main();
            Toastr.fire({
                icon: 'info',
                title: "&nbsp; Debe usar una opción de consulta"
            });
        } else if (form.stock.value !== '' && form.posicion.value !== '') {
            form.equipo.value = '';

            var Toastr = main();
            Toastr.fire({
                icon: 'info',
                title: "&nbsp; Debe usar una de las dos opciones de consulta",
            });
        } else {

            $.ajax({
                type: form.method,
                url: form.action,
                data: $(form).serialize(),
                dataType: 'json',
                success: function (data) {

                    if (data[2]) {
                        $('#tablelistado').fadeIn();
                        $("#mapsvg").empty();
                        $("#mapsvg").append(data[1]).fadeIn();
                        $('[data-bs-toggle="tooltip"]').tooltip();
                    } else {
                        $('#tablelistado').fadeIn();
                        $("#mapsvg").empty();
                    }

                    if ($.fn.dataTable.isDataTable('#datatable-posicion')) {
                        $('#tablelistado').fadeOut();
                        $('#datatable-posicion').DataTable().destroy();
                    }

                    $('#datatable-posicion').DataTable({
                        "initComplete": function (settings, json) {
                            $('#tablelistado').fadeIn();
                        },
                        responsive: true,
                        "ordering": false,
                        "autoWidth": false,
                        data: data[0],
                        "lengthMenu": [5, 20, 50, 75],
                        "bPaginate": true,
                        "bProcessing": true,
                        "columns": [
                            { mData: 'posicion' },
                            { mData: 'equipo' },
                            { mData: 'monitor' },
                            { mData: 'periferico' },
                            { mData: 'responsable' },
                            { mData: 'acciones' },
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
    }
});

$("#stockequipo").change(function () {
    var url, data;
    url = $(this).data('href');
    data = {
        sede: $(this).val(),
        _token: $("input[name=_token]").val(),
    };
    $.ajax({
        url: url,
        method: "POST",
        data: data,
        dataType: "JSON",
        success: function (data) {
            $('.displayequipo').fadeOut();
            $('#datatable-equipos').DataTable().destroy();
            $('.displayequipo').fadeIn();
            $('#datatable-equipos').addClass('table');
            $('#datatable-equipos').DataTable({
                "data": data[0],
                dataSrc: "",
                "lengthMenu": [5, 20, 50, 75],
                "bPaginate": true,
                "bProcessing": true,
                "columns": [
                    { data: 'tipo' },
                    { data: 'asignado' },
                    { data: 'available' },
                    { data: 'bad' },
                    { data: 'total' }
                ]
            });

            //$('#datatable-equipos').DataTable().refresh();

            var equipos = data[1];
            if (equipos.length === 0) {
                $('#equipos').empty();
                $('#equipos').select2({ placeholder: "No existen criterios de búsqueda" });
            } else {
                $('#equipos').empty();
                $('#equipos').append($("<option></option>").attr("value", '').text("[Seleccione]"));
                for (i = 0; i < equipos.length; i++) {
                    $('#equipos').append($("<option></option>").attr("value", equipos[i].id).text(equipos[i].descripcion));
                }
                $('#equipos').select2({ 'placeholder': "[Seleccione una o mas Opciones]" });
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

$("#stockperiferico").change(function () {
    //alert("cambia");
    var url, data;
    url = $(this).data('href');
    data = {
        almacen: $(this).val(),
        _token: $("input[name=_token]").val()
    };
    if ($.fn.dataTable.isDataTable('#datatable_perifericos')) {
        $('.displayperifericos').fadeOut();
        $('#datatable_perifericos').DataTable().destroy();
        $('.displayperifericos').fadeIn();
    }
    $.ajax({
        url: url,
        method: "POST",
        data: data,
        dataType: "JSON",
        success: function (data) {
            $('#datatable_perifericos').DataTable({
                responsive: true,
                "ordering": false,
                "autoWidth": false,
                data: data[0],
                "lengthMenu": [5, 20, 50, 75],
                "bPaginate": true,
                "bProcessing": true,
                "columns": [
                    { mData: 'tipo' },
                    { mData: 'conexion' },
                    { mData: 'asignado' },
                    { mData: 'available' },
                    { mData: 'bad' },
                    { mData: 'total' }
                ]
            });
            if (data[1].length !== 0) {
                $.each(data[1], function (key, value) {
                    console.log(key, value);
                    $('#display' + key.toLowerCase()).show();
                    if (value === 'disabled') {
                        //si el valor buscado no esta disponible ///
                        $('#new' + key.toLowerCase() + "msg").empty();
                        $('#new' + key.toLowerCase()).prop('disabled', true);
                        $('#new' + key.toLowerCase()).prop("checked", false);
                        $('#new' + key.toLowerCase() + "msg").append('<br/><br/><i class="fas fa-ban"></i><br/><p>No Disponible</p>');
                        $('#new' + key.toLowerCase()).rules('add', {
                            alMenosUnoSeleccionado: true
                        });

                    } else {
                        /// si el periferico esta diposnible
                        $('#display' + key.toLowerCase()).show();
                        $('#new' + key.toLowerCase() + "msg").empty();
                        $('#new' + key.toLowerCase()).prop('disabled', false);
                        $('#new' + key.toLowerCase()).prop("checked", false);
                        $('.check').attr('checked', false);
                        $('#new' + key.toLowerCase()).rules('add', {
                            alMenosUnoSeleccionado: true
                        });
                    }
                });
            } else {
                //console.log("aca");
                $('.clearmsg').empty();
                $('.check').attr('disabled', true);
                $('.check').prop("checked", false);
                $('.clearmsg').append('<br/><br/><i class="fas fa-ban"></i><br/><p>No Disponible</p>');
                $('.check').rules('add', {
                    alMenosUnoSeleccionado: true
                });
            }
        },
        error: function (data) {
            var Toastr = main();
            Toastr.fire({
                icon: 'error',
                title: data.responseJSON,
            });
        }
    });
});

$("#mapsede").change(function () {
    var url, data;
    url = $(this).data('href');
    data = {
        sede: $(this).val(),
        _token: $("input[name=_token]").val(),
    };
    $.ajax({
        url: url,
        method: "POST",
        data: data,
        dataType: "JSON",
        success: function (data) {
            $("#mapsvg").empty();
            $('#posicionsel').empty();
            $('#posicionsel').attr("disabled", true);
            $(".mapashow").fadeIn();
            $("#mapsvg").append(data).fadeIn();
            $('[data-toggle="tooltip"]').tooltip();
            $.getScript("../../js/inventario/mapas.js");


        },
        error: function (data) {
            $('#posicionsel').empty();
            $("#mapsvg").empty();
            $(".mapashow").hide();
            var Toastr = main();
            Toastr.fire({
                icon: 'error',
                title: data.responseJSON.message,
            });
        }
    });
});

///parte de adminsitracion y eliminacion
$('#area_trabajo').on('change', function () {

    var area = $(this).val();
    var url = $(this).data('href');
    var _token = $("input[name='_token']").val();
    $.ajax({
        type: 'POST',
        url: url,
        data: { area: area, _token: _token },
        dataType: 'json',
        success: function (data) {
            $('#carteras').empty();
            $('#carteras').append($("<option></option>").attr("value", '').text("[SELECCIONE]"));
            $('#skill').empty();
            $('#skill').append($("<option></option>").attr("value", '').text("[SELECCIONE UNIDAD/CAMPAÑA]"));
            for (i = 0; i < data[0].length; i++) {
                $('#carteras').append($("<option></option>").attr("value", data[0][i].id).text(data[0][i].nombre_cartera));
            }
            $('#gerentecargo').empty();
            $('#gerentecargo').append($("<option></option>").attr("value", '').text("[SELECCIONE]"));
            for (i = 0; i < data[1].length; i++) {
                $('#gerentecargo').append($("<option></option>").attr("value", data[1][i].id).text(data[1][i].primer_nombre + ' ' + data[1][i].primer_apellido));
            }
        },
        error: function (data) {
            //  console.log(data);
            var Toastr = main();
            Toastr.fire({
                icon: 'error',
                title: data.responseJSON.message,
            });
        }
    });
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
        },
        error: function (data) {
            //  console.log(data);
            var Toastr = main();
            Toastr.fire({
                icon: 'error',
                title: data.responseJSON.message,
            });
        }
    });
});

///INSERTAR DATOS DE POSICION
$("#FromPosicionAdd").validate({
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

        $('.nav-tabs').find('small.required').remove();
        $('.tab-content .tab-pane:has(.is-invalid)').each(function () {
            var id = $(this).attr('id');
            $('.nav-tabs').find('a.nav-link[aria-controls=' + id + ']').append('<small class="required" style="color:red;"> <b>*</b></small>');
        });
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass('error is-invalid text-sm');
    },
    submitHandler: function (form) {
        event.preventDefault();
        Swal.fire({
            title: "Se procedera a vincular el Monitor, Equipo, Perifericos a la(s) posicion(es) seleccionada(s). ¿Esta de Acuerdo?",
            showDenyButton: false,
            showCancelButton: true,
            confirmButtonText: "Procesar",
            denyButtonText: "Regresar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {
                $(':input[type="submit"]').prop('disabled', true);
                $('#posicionsel').attr('disabled', false);
                $('#posicionsel').attr('readonly', true);
                form.submit();
            }
        });
    }
});

///para actualizar las etiquetas de los componentes de la posicion
function EtiquetasChange(form, key) {

    $('#' + form + key).find('button[type="submit"]').each(function () {
        $(this).removeAttr('disabled', 'disabled');
    });
    RemplazoDeComponentesEquipos.init(form + key);
}
//agregar nuevos monitores/perifericos/equipos
function AddPosicion(type) {

    var formulario = 'modal' + type;

    $('#new').modal();

    $('#new').find('.modal-title').each(function () {
        $(this).html('Asociar nuevo ' + type + ' a la posición.');
    });

    $('#new').find('form').each(function () {
        $(this).hide();
    });

    $('#' + formulario).show();
    RemplazoDeComponentesEquipos.init(formulario);
}
///actualizar posicion, estructura y desvinculacion
$("#posicion_update").validate({
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
            title: "¿Esta de Acuerdo con los Cambios Realizados?",
            showDenyButton: false,
            showCancelButton: true,
            confirmButtonText: "Procesar",
            denyButtonText: "Regresar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {

                $(form).find('button[type="submit"]').each(function () {
                    $(this).attr('disabled', 'disabled');
                });
                form.submit();
            }
        });
    }
});

$('.sedemigration').change(function () {

    var input = $(this).attr('id');
    var url = $(this).data('href');

    var selectautoComplete = input === "stocksalida" ? "sedesalida" : "sededestino";

    var data = {
        sede: $(this).val(),
        _token: $("input[name=_token]").val(),
    };
    $.ajax({
        type: 'POST',
        url: url,
        data: data,
        dataType: 'json',
        success: function (data) {
            $('#' + selectautoComplete).empty();
            $('#' + selectautoComplete).append($("<option></option>").attr("value", "").text("[SELECCIONE]"));

            for (i = 0; i < data.length; i++) {
                $('#' + selectautoComplete).append($("<option></option>").attr("value", data[i].id).text(data[i].posicion + '| Equipo: ' + data[i].equipo + ' | ' + data[i].assignad));
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

///crear migracion de posiciones
$("#migrationCreate").validate({
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
            title: "CONFIRMACION DE ACCION",
            html: "¿Esta de acuerdo en procesar la migración de las pociones seleccionadas y sus items asociados?. \n\
            Esto ocaciona que las posiciones no puedan ser modificadas hasta finalizar el proceso. \n\
            ¿Esta de acuerdo?",
            showDenyButton: false,
            showCancelButton: true,
            confirmButtonText: "Procesar",
            denyButtonText: "Regresar",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {

                $(form).find('button[type="submit"]').each(function () {
                    $(this).attr('disabled', 'disabled');
                });
                form.submit();
            }
        });
    }
});
///Ver migraciones
$('.viewmigrations').click(function () {

    var input = $(this).attr('id');
    var url = $(this).data('href');
    var data = { _token: $("input[name=_token]").val() };

    if ($.fn.dataTable.isDataTable('#datatable-migracion')) {
        $('#datatable-migracion').DataTable().destroy();
    }

    $('#datatable-migracion').DataTable({
        "initComplete": function (settings, json) {
            $('#viewMigracionModal').modal({
                backdrop: 'static',
                keyboard: false
            });
        },
        responsive: true,
        ordering: true,
        autoWidth: false,
        "ajax": {
            "url": url,
            "dataType": "json",
            "type": "POST",
            "data": data,
            dataSrc: ""
        },
        "lengthMenu": [10, 20, 50, 75],
        "bPaginate": true,
        "bProcessing": true,
        "columns": [
            { mData: 'migrar' },
            { mData: 'destino' },
            { mData: 'equipo' },
            { mData: 'monitor' },
            { mData: 'periferico' },
        ]
    });

});
///procesar migraciones
$('.storageMigration').click(function () {

    var input = $(this).attr('id');
    var url = $(this).data('href');
    var data = { _token: $("input[name=_token]").val() };

    Swal.fire({
        title: "CONFIRMACION DE ACCION",
        html: "Se procedera a iniciar las migración de posiciones en el sistema. al iniciar esta acción, las posiciones de la solicitud se veran afectadas y no podra visualizar nuevamente la solicitud. ¿Esta de acuerdo?",
        showDenyButton: false,
        showCancelButton: true,
        confirmButtonText: "Procesar",
        denyButtonText: "Regresar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: 'PUT',
                url: url,
                data: data,
                dataType: 'json',
                success: function (data) {
                    var Toastr = main();
                    Toastr.fire({
                        icon: 'success',
                        title: data,
                    });
                    setTimeout(function () {
                        window.location.reload(true);
                    }, 5000);
                },
                error: function (data) {
                    var Toastr = main();
                    Toastr.fire({
                        icon: 'error',
                        title: data.responseJSON.message,
                    });
                }
            });

        }
    });
});

$("#qrSearch").validate({
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

                if ($.fn.dataTable.isDataTable('#datatable-etiquetas')) {
                    $('#tablelistado').fadeOut();
                    $('#datatable-etiquetas').DataTable().destroy();
                }

                $('#datatable-etiquetas').DataTable({
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
                        { mData: 'seleccione' },
                        { mData: 'identificador' },
                        { mData: 'estatus' },
                    ]
                });

                $('#tipoqr').val($("#tipo").val());

                $('.check').rules('add', {
                    alMenosUnoSeleccionado: true
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

$("#qr_generate").validate({
    ignore: [],
    errorPlacement: function (error, element) {
        var Toastr = main();
        Toastr.fire({ icon: 'error', title: error.text() });
    },
    highlight: function (error, element) {
        $(element).closest('.form-control').addClass('is-invalid');
    },
    unhighlight: function (element) {
        $(element).closest('.form-control').removeClass('is-invalid');
        $(element).closest('.form-control').addClass('is-valid');
    },
    submitHandler: function (form, event) {
        $(':input[type="submit"]').prop('disabled', true);
        form.submit();
    }

});

$("#reporteGenerate").validate({
    ignore: [],
    errorElement: 'span',
    validClass: "is-valid",
    errorPlacement: function (error, element) {
        error.addClass('is-invalid text-sm');
        element.closest('.form-group').append(error);
    },
    highlight: function (error, element) {
        $(element).closest('.form-control').addClass('is-invalid');
    },
    unhighlight: function (element) {
        $(element).closest('.form-control').removeClass('is-invalid');
        $(element).closest('.form-control').addClass('is-valid');
    },
    submitHandler: function (form, event) {
        $(':input[type="submit"]').prop('disabled', true);
        form.submit();
    }

});