$(function () {
    if ($('.mesdate').length != 0) {
        $('.mesdate').datetimepicker({
            format: 'MM/YYYY',
            maxDate: moment(),
        });
    }
});

$('#nomina').on('change', function () {

    var selectedOption = $(this).val();
    if (selectedOption == "todos") {
        $('#tprecibo option').hide(); // Oculta todas las opciones primero

        // Muestra la opción sin valor
        $('#tprecibo option[value=""]').show();
        
        var seenValues = [];
        $('#tprecibo option').each(function() {
            var val = $(this).val();
            // Verifica que el valor no esté vacío y no se haya mostrado ya
            if (val !== "" && $.inArray(val, seenValues) === -1) {
                seenValues.push(val);
                $(this).show(); // Muestra solo el primer elemento con este valor
            }
        });

        $('#tprecibo').val('');
        $('#tprecibo').removeAttr('disabled');

        return;
    } else {
        $('#tprecibo option').hide(); // Oculta todas las opciones primero

        // Muestra la opción sin valor
        $('#tprecibo option[value=""]').show();
        // Muestra las opciones que coinciden con el valor seleccionado
        $('#tprecibo option[data-nomina="' + selectedOption + '"]').show();
        $('#tprecibo').val('');
        $('#tprecibo').removeAttr('disabled');
    }
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
        optionsToHide[selectedOption].forEach(function (value) {
            console.log(value);
            $('#corte option[value="' + value + '"]').hide();
        });
    }
});

$('#SearchPeriodosActive').validate({
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
        event.preventDefault();
        $.ajax({
            type: form.method,
            url: form.action,
            data: $(form).serialize(),
            dataType: 'json',
            success: function (data) {

                if ($.fn.dataTable.isDataTable('#periodostable')) {
                    $('#displayTable').fadeOut();
                    $('#periodostable').DataTable().destroy();
                }
                $('#periodostable').DataTable({
                    "initComplete": function (settings, json) {
                        $('#displayTable').fadeIn();
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
                        { data: 'corte' },
                        { data: 'tprecibo' },
                        { data: 'nomina' },
                        { data: 'descripcion' },
                        { data: 'estatus' },
                        { data: 'accion' },
                    ]
                });

                $('.periodoactive').bootstrapSwitch({ offText: "Off", onText: "On" });

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
});

$('#ReportesGenerates').validate({
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

/// cambiar estatus de los periodos disponibles.
function ChangePeriodoActive(value) {
    var input, url, data;
    input = $('#periodoactive' + value);
    if (input.bootstrapSwitch('state') === true) {
        url = input.data("href") + "/1";
    } else {
        url = input.data("href") + "/0";
    }
    data = { _token: $("input[name=_token]").val() }
    $.ajax({
        type: 'PUT',
        url: url,
        data: data,
        dataType: 'json',
        success: function (data) {
            var Toastr = main();
            $(function () {
                Toastr.fire({
                    icon: data.type,
                    title: "&nbsp;" + data.menssage,
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

//eliminar periodo cargado
function DeletePeriodo(value) {

    Swal.fire({
        title: "CONFIRMAR ACCIONES",
        html: "Esta a punto de eliminar un lote de recibos de pagos, tenga en cuenta que el periodo sera cargado nuevamente en la proxima extracción ¿Esta de acuerdo en continuar?",
        showDenyButton: false,
        showCancelButton: true,
        confirmButtonText: "Procesar",
        denyButtonText: "Regresar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            $('#deleted' + value).submit();
        }
    });
}