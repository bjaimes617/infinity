$.validator.addMethod(
    "alMenosUnoSeleccionado",
    function (value, element) {
        return $(".check:checked").length > 0;
    },
    "Seleccione al menos un elemento."
);

$(function () {
    if ($(".select2").length != 0) {
        $(".select2").select2({ theme: "bootstrap4" });
    }

    //selector de mes de importar comisones
    if ($(".datetime").length != 0) {
        $(".datetime").each(function () {
            var meses = $(this).data("valormes"); // ej. 1
            var min = moment().subtract(meses, "months").startOf("month"); // primer día del mes mínimo
            var max = moment().endOf("month"); // último día del mes actual

            $(this).datetimepicker({
                format: "MM/YYYY",
                minDate: min,
                maxDate: max,
                viewMode: "months", // para que solo muestre meses y años
                // opcional: ignoreReadonly: true, si el input es readonly y quieres que funcione
            });
            $(this).data("DateTimePicker").date(moment(max, "MM/YYYY"));
        });
    }

    if ($(".datetimereport").length != 0) {
        $(".datetimereport").each(function () {
            var meses = $(this).data("valormes"); // ej. 1
            var min = moment().subtract(meses, "months").startOf("month"); // primer día del mes mínimo
            var max = moment().endOf("month"); // último día del mes actual

            $(this).datetimepicker({
                format: "MM/YYYY",
                minDate: min,
                maxDate: max,
                viewMode: "months", // para que solo muestre meses y años
                // opcional: ignoreReadonly: true, si el input es readonly y quieres que funcione
            });
            $(this).data("DateTimePicker").date(moment(max, "MM/YYYY"));
        });
    }

    if ($(".calendarfecha").length != 0) {
        $(".calendarfecha").each(function () {
            $(this).datetimepicker({
                format: "DD/MM/YYYY",
                maxDate: moment(),
            });
        });
    }
    //selector de mes dde auditoria
    if ($(".timeaudit").length != 0) {
        var fechaInicial = moment();
        $(".timeaudit").each(function () {
            console.log("iniciando " + $(this).attr("name"));
            $(this).val(fechaInicial.format("MM/YYYY"));
            $(this).datetimepicker({
                format: "MM/YYYY",
                maxDate: moment(),
            });
        });
    }

    if ($("#datatable-incidencias").length != 0) {
        $("#datatable-incidencias").DataTable({
            lengthMenu: [10, 20, 15, 30, 50, 60],
            responsive: true,
            lengthChange: true,
            autoWidth: false,
            ordering: true,
            //"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        });
        $(".check").rules("add", {
            alMenosUnoSeleccionado: true,
        });
    }

    $("#select_all").on("change", function () {
        $(".check").prop("checked", $(this).prop("checked"));
    });
});

$(function () {
    bsCustomFileInput.init();
});

//funcion recursiva que aplicara los filtros en el input mes y periodo
//basados en los tres tipos de periodos semanales, quincenales y mixto
//si se actiba tipo periodo solo aplicara para semanal y quincena, en el caso del Skill entrara en los 3 casos
function AplicaFiltrosPostConsultas(data) {
    console.log(data);
    switch (data.tipo_periodo) {
        case "Semanal":
            $("#mes").attr("disabled", false);
            $("#periodo").empty();
            $("#periodo").append(
                $("<option></option>").attr("value", "").text("[SELECCIONE]")
            );
            ///imprimimos los valores del arreglo semanal
            $.each(data.arreglo_semanal, function (key, value) {
                $("#periodo").append(
                    $("<option></option>").attr("value", key).text(value)
                );
            });
            ///seleccionamos la semana en curso
            $('#periodo option[value="' + data.semana_en_curso + '"]').prop(
                "selected",
                true
            );
            break;
        case "Quincenal":
            //bloquea enlace de archivo tipo A y bloquea formatos b y C
            $("#enlace1").removeClass("disabled");
            $("#enlace2").addClass("disabled");
            $("#enlace3").addClass("disabled");

            $("#tipo_periodo").prop("disabled")
                ? $("#tipo_periodo").prop("disabled", true) // Si ya está disabled, mantenerlo true
                : $("#tipo_periodo").prop("disabled", false); // Si no está disabled, mantenerlo false
            $("#tipo_periodo").attr("required", false);

            $("#periodo").empty();
            $("#periodo").append(
                $("<option></option>").attr("value", "").text("[CORTE]")
            );

            ///liberamos el selector de mes
            $("#mes").attr("disabled", false);
            $("#mes").attr("required", true);

            if (data.quincena_en_curso === 3) {
                ///imprime el tipo de corte a seleccionarse
                $.each(data.opcion_corte_show, function (key, value) {
                    $("#periodo").append(
                        $("<option></option>")
                            .attr("value", key)
                            .text(value)
                            .prop("selected", false) // Marca como seleccionada la única opción
                    );
                });

                $("#periodo").attr("required", true);
            } else {
                $.each(data.opcion_corte_show, function (key, value) {
                    $("#periodo").append(
                        $("<option></option>")
                            .attr("value", key)
                            .text(value)
                            .prop("selected", true) // Marca como seleccionada la única opción
                    );
                });
            }
            break;
        case "Mixto":
            $("#tipo_periodo").attr("disabled", false);
            $("#tipo_periodo").attr("required", true);

            $("#enlace1").removeClass("disabled");
            $("#enlace2").removeClass("disabled");
            $("#enlace3").removeClass("disabled");
            break;
        default:
            break;
    }
}

$("#area_trabajo").on("change", function () {
    var url = $(this).data("href");
    var data = { area: $(this).val(), _token: $("input[name=_token]").val() };
    $("#carteras").empty();
    console.log("funcion general GERENCIA");
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: "json",
        success: function (data) {
            var value = "[SELECCIONE]";
            $("#carteras").append(
                $("<option></option>").attr("value", "").text(value)
            );
            for (i = 0; i < data.length; i++) {
                $("#carteras").append(
                    $("<option></option>")
                        .attr("value", data[i].id)
                        .text(data[i].nombre_cartera)
                );
            }
        },
        error: function () {
            var Toastr = main();
            Toastr.fire({
                icon: "error",
                title: "&nbsp; Opción Invalida.",
            });
        },
    });
});

$("#carteras").on("change", function () {
    $(".enlaces").addClass("disabled");
    $("#tipo_periodo").val("");
    $("#tipo_periodo").prop("disabled", true);
    ///siempre mantenemos lso botones desactivados
    var url = $(this).data("href");
    cartera = $(this).val();
    $("#skill").empty();

    var data = { cartera: cartera, _token: $("input[name=_token]").val() };
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: "json",
        success: function (data) {
            var value = "[SELECCIONE]";
            $("#skill").append(
                $("<option></option>").attr("value", "").text(value)
            );
            for (i = 0; i < data.length; i++) {
                $("#skill").append(
                    $("<option></option>")
                        .attr("value", data[i].id)
                        .text(data[i].nombre_skill)
                );
            }
        },
        error: function (data) {
            $(".enlaces").addClass("disabled");
            var Toastr = main();
            Toastr.fire({
                icon: "error",
                title: data.responseJSON.message,
            });
        },
    });
});

$("#skill").on("change", function () {
    var url = $(this).data("href");
    var data = {
        area: $("#area_trabajo").val(),
        cartera: $("#carteras").val(),
        skill: $("#skill").val(),
        _token: $("input[name=_token]").val(),
    };
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: "json",
        success: function (data) {
            if (data.maxDate !== null) {
                $("#mes").val(data.maxDate);
            }
            $(".enlaces").addClass("disabled");
            data.tipo_formatos.forEach(function (item) {
                $("#" + item).removeClass("disabled");
            });

            AplicaFiltrosPostConsultas(data);
        },
        error: function (data) {
            var Toastr = main();
            Toastr.fire({
                icon: "error",
                title: data.responseJSON.message,
            });
        },
    });
});

$("#tipo_periodo").on("change", function () {
    ///siempre mantenemos lso botones desactivados
    var url = $(this).data("href");
    var tipo_periodo = $(this).val();

    var data = {
        tipo_periodo: tipo_periodo,
        skill: $("#skill").val(),
        _token: $("input[name=_token]").val(),
    };
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: "json",
        success: function (data) {
            AplicaFiltrosPostConsultas(data);
        },
        error: function (data) {
            var Toastr = main();
            Toastr.fire({
                icon: "error",
                title: data.responseJSON.message,
            });
        },
    });
});

$("#mes").on("blur", function () {
    ///siempre mantenemos lso botones desactivados
    var url = $(this).data("href");
    var tipo_periodo = $(this).val();

    var data = {
        mes: $(this).val(),
        tipo_periodo: $("#tipo_periodo").val(),
        _token: $("input[name='_token']").val(),
        skill: $("#skill").val(),
    };

    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: "json",
        success: function (data) {
            AplicaFiltrosPostConsultas(data);
        },
        error: function () {
            var Toastr = main();
            Toastr.fire({
                icon: "error",
                title: data.responseJSON.message,
            });
        },
    });
});

$("#comisionesstore").validate({
    lang: "es",
    errorElement: "span",
    validClass: "is-valid",
    errorPlacement: function (error, element) {
        error.addClass("is-invalid text-sm");
        element.closest(".form-group").append(error);
    },
    highlight: function (element, errorClass, validClass) {
        $(element).addClass("is-invalid text-sm");
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass("error is-invalid text-sm");
    },
    submitHandler: function (form) {
        var mensaje =
            "Se realizara la Carga de Variables o Comisiones para la Siguiente Estructura: <br />\n " +
            "Gerencia: <b>" +
            $("#area_trabajo option:selected").text() +
            "</b><br /> \n " +
            "Skill: <b>" +
            $("#skill option:selected").text() +
            "</b><br /> \n " +
            "Mes: <b>" +
            $("#mes").val() +
            "</b><br /> \n " +
            "Periodo: <b>" +
            $("#periodo option:selected").text() +
            "</b><br /> \n " +
            " Tenga en cuenta, que no podra Cargar Variables o Comisiones bajo la estructura seleccionada si ya fue Pagada. <br /> \n " +
            "¿Esta de Acuerdo con Esta Información?";

        Swal.fire({
            title: "!Atención!",
            html: mensaje,
            icon: "info",
            showCancelButton: true,
            showConfirmButton: true,
            cancelButtonText: "Cancelar",
            confirmButtonText: "Confirmar",
        }).then((result) => {
            // Se agrega el método .then()
            if (result.isConfirmed) {
                // Aquí se valida si se presionó 'Cancelar'
                form.submit();
            }
        });
    },
});

//auditorias
$("#comision_payment").validate({
    lang: "es",
    errorElement: "span",
    validClass: "is-valid",
    errorPlacement: function (error, element) {
        error.addClass("is-invalid text-sm");
        element.closest(".form-group").append(error);
    },
    highlight: function (element, errorClass, validClass) {
        $(element).addClass("is-invalid text-sm");
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass("error is-invalid text-sm");
    },
    submitHandler: function (form) {
        event.preventDefault();
        $.ajax({
            type: form.method,
            url: form.action,
            data: $(form).serialize(),
            dataType: "json",
            success: function (data) {
                if ($.fn.dataTable.isDataTable("#datatable-auditoria")) {
                    $(".tableaudit").fadeOut();
                    $("#datatable-auditoria").DataTable().destroy();
                }
                $("#datatable-auditoria").DataTable({
                    initComplete: function (settings, json) {
                        $(".tableaudit").fadeIn();
                    },
                    responsive: true,
                    ordering: true,
                    autoWidth: false,
                    data: data[1],
                    dataSrc: "",
                    lengthMenu: [10, 20, 50, 75],
                    bPaginate: true,
                    bProcessing: true,
                    columns: [
                        { data: "check" },
                        { data: "nombre" },
                        { data: "gerencia" },
                        { data: "campana" },
                        { data: "skill" },
                        { data: "tipo_periodo" },
                        { data: "fecha" },
                        { data: "acciones" },
                    ],
                    createdRow: function (row, data, dataIndex) {
                        $(row).attr("data-id", data.id); // suponiendo que 'id' es el campo único
                    },
                });
                $(".check").rules("add", {
                    alMenosUnoSeleccionado: true,
                });
                $("#auditar").attr("disabled", data[0]);
            },
            error: function (data) {
                var Toastr = main();
                Toastr.fire({
                    icon: "error",
                    title: data.responseJSON.message,
                });
            },
        });
    },
});

function AccionesAudit(button, value) {
    switch (button) {
        case "delete":
            var url = $("#delete" + value).data("href");
            var typedata = "json";
            var data = { _token: $("input[name=_token]").val() };
            var method = "delete";
            var mensaje =
                "Sera eliminado el Lote seleccionado y sus datos asociados, tenga encuenta que esta acción no puede revertirse. ¿Esta de Acuerdo?";
            break;
        case "restore":
            var url = $("#restore" + value).data("href");
            var typedata = "json";
            var data = { _token: $("input[name=_token]").val() };
            var method = "PUT";
            var mensaje =
                "Se realizara el Reverso de Estatus de Auditado a No Procesado. ¿Esta de Acuerdo?";
            break;
        case "deleteBancos":
            var url = $("#delete" + value).data("href");
            var typedata = "json";
            var data = { _token: $("input[name=_token]").val() };
            var method = "DELETE";
            var mensaje =
                "Al Eliminar el consolidado de Banco, se revertiran los estatus y debera cosolidar nuevamente. ¿Esta de Acuerdo?";
            break;
            break;
        case "discardbank":
            var url = $("#restore" + value).data("href");
            var typedata = "json";
            var data = { _token: $("input[name=_token]").val() };
            var method = "PUT";
            var mensaje =
                "Se revertira el Lote de Banco de Pagado a Auditado y debera consolidar nuevamente. ¿Esta de Acuerdo?";
            break;
    }

    Swal.fire({
        title: "!Atención!",
        text: mensaje,
        icon: "info",
        showCancelButton: true,
        showConfirmButton: true,
        cancelButtonText: "Cancelar",
        confirmButtonText: "Confirmar",
    }).then((result) => {
        // Se agrega el método .then()
        if (result.isConfirmed) {
            // Aquí se valida si se presionó 'Cancelar'

            $.ajax({
                type: method,
                url: url,
                data: data,
                dataType: typedata,
                success: function (data) {
                    var Toastr = main();
                    Toastr.fire({
                        icon: "success",
                        title: data,
                    });

                    if (
                        button !== "restore" ||
                        button === "deleteBancos" ||
                        button === "discardbank"
                    ) {
                        $('tr[data-id="' + value + '"]').hide();
                    } else {
                        location.reload();
                    }
                },
                error: function () {
                    var Toastr = main();
                    Toastr.fire({
                        icon: "error",
                        title: data.responseJSON.message,
                    });
                },
            });
        }
    });
}

$("#audotirastore").validate({
    lang: "es",
    errorElement: "span",
    validClass: "is-valid",
    errorPlacement: function (error, element) {
        $(element).addClass("is-invalid text-sm");

        // element.closest('.form-group').append(error);
        var Toastr = main();
        Toastr.fire({
            icon: "error",
            title: error,
        });
    },
    highlight: function (element, errorClass, validClass) {
        $(element).addClass("is-invalid text-sm");
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass("error is-invalid text-sm");
    },
    submitHandler: function (form) {
        form.submit();
    },
});

//procesar consolidados de pago
$("#processpayment").validate({
    lang: "es",
    errorElement: "span",
    validClass: "is-valid",
    errorPlacement: function (error, element) {
        error.addClass("is-invalid text-sm");
        element.closest(".form-group").append(error);
    },
    highlight: function (element, errorClass, validClass) {
        $(element).addClass("is-invalid text-sm");
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass("error is-invalid text-sm");
    },
    submitHandler: function (form) {
        event.preventDefault();
        $.ajax({
            type: form.method,
            url: form.action,
            data: $(form).serialize(),
            dataType: "json",
            success: function (data) {
                if ($.fn.dataTable.isDataTable("#datatable-payments")) {
                    $(".tableaudit").fadeOut();
                    $("#datatable-payments").DataTable().destroy();
                }
                $("#datatable-payments").DataTable({
                    initComplete: function (settings, json) {
                        $(".tableaudit").fadeIn();
                    },
                    responsive: true,
                    ordering: false,
                    autoWidth: false,
                    data: data[1],
                    dataSrc: "",
                    lengthMenu: [10, 20, 50, 75],
                    bPaginate: true,
                    bProcessing: true,
                    columns: [
                        { data: "check" },
                        { data: "nombre" },
                        { data: "gerencia" },
                        { data: "campana" },
                        { data: "skill" },
                        { data: "tipo_periodo" },
                        { data: "fecha" },
                    ],
                    createdRow: function (row, data, dataIndex) {
                        $(row).attr("data-id", data.id); // suponiendo que 'id' es el campo único
                    },
                });
                console.log($(".check").length);

                $(".check").rules("add", {
                    alMenosUnoSeleccionado: true,
                });

                $("#pagosstore").prop("disabled", data[0]);
            },
            error: function (data) {
                var Toastr = main();
                Toastr.fire({
                    icon: "error",
                    title: data.responseJSON.message,
                });
            },
        });
    },
});

$("#paymentstore").validate({
    lang: "es",
    errorElement: "span",
    validClass: "is-valid",
    errorPlacement: function (error, element) {
        $(element).addClass("is-invalid text-sm");
        console.log(element);
        if (element.is('input[type="checkbox"]')) {
            var Toastr = main();
            Toastr.fire({
                icon: "error",
                title: error,
            });
        } else {
            element.closest(".form-group").append(error);
        }
        //
    },
    highlight: function (element, errorClass, validClass) {
        $(element).addClass("is-invalid text-sm");
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass("error is-invalid text-sm");
    },
    submitHandler: function (form) {
        form.submit();
    },
});

///pagar consolidados de banco
$("#processconsolidado").validate({
    lang: "es",
    errorElement: "span",
    validClass: "is-valid",
    errorPlacement: function (error, element) {
        error.addClass("is-invalid text-sm");
        element.closest(".form-group").append(error);
    },
    highlight: function (element, errorClass, validClass) {
        $(element).addClass("is-invalid text-sm");
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass("error is-invalid text-sm");
    },
    submitHandler: function (form) {
        event.preventDefault();
        $.ajax({
            type: form.method,
            url: form.action,
            data: $(form).serialize(),
            dataType: "json",
            success: function (data) {
                if ($.fn.dataTable.isDataTable("#datatable-pagar")) {
                    $(".tableaudit").fadeOut();
                    $("#datatable-pagar").DataTable().destroy();
                }
                $("#datatable-pagar").DataTable({
                    initComplete: function (settings, json) {
                        $(".tableaudit").fadeIn();
                    },
                    responsive: true,
                    ordering: false,
                    autoWidth: false,
                    data: data[1],
                    dataSrc: "",
                    lengthMenu: [10, 20, 50, 75],
                    bPaginate: true,
                    bProcessing: true,
                    columns: [
                        { data: "check" },
                        { data: "nombre" },
                        { data: "fecha" },
                        { data: "estatus" },
                        { data: "acciones" },
                    ],
                    createdRow: function (row, data, dataIndex) {
                        $(row).attr("data-id", data.id); // suponiendo que 'id' es el campo único
                    },
                });

                $(".check").rules("add", {
                    alMenosUnoSeleccionado: true,
                });

                $("#banco").rules("add", {
                    alMenosUnoSeleccionado: true,
                });

                $("#storeconsolidadopay").prop("disabled", data[0]);
            },
            error: function (data) {
                var Toastr = main();
                Toastr.fire({
                    icon: "error",
                    title: data.responseJSON.message,
                });
            },
        });
    },
});
$("#storeconsolidado").validate({
    lang: "es",
    errorElement: "span",
    validClass: "is-valid",
    errorPlacement: function (error, element) {
        $(element).addClass("is-invalid text-sm");
        console.log(element);
        if (element.is('input[type="checkbox"]')) {
            var Toastr = main();
            Toastr.fire({
                icon: "error",
                title: error,
            });
        } else {
            element.closest(".form-group").append(error);
        }
        //
    },
    highlight: function (element, errorClass, validClass) {
        $(element).addClass("is-invalid text-sm");
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass("error is-invalid text-sm");
    },
    submitHandler: function (form) {
        form.submit();
    },
});

$("#procesarincidencias").validate({
    lang: "es",
    errorElement: "span",
    validClass: "is-valid",
    errorPlacement: function (error, element) {
        error.addClass("is-invalid text-sm");
        element.closest(".form-group").append(error);
    },
    highlight: function (element, errorClass, validClass) {
        $(element).addClass("is-invalid text-sm");
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass("error is-invalid text-sm");
    },
    submitHandler: function (form) {
        event.preventDefault();
        $.ajax({
            type: form.method,
            url: form.action,
            data: $(form).serialize(),
            dataType: "json",
            success: function (data) {
                if ($.fn.dataTable.isDataTable("#datatable-pagados")) {
                    $(".tablepagados").fadeOut();
                    $("#datatable-pagados").DataTable().destroy();
                }
                $("#datatable-pagados").DataTable({
                    initComplete: function (settings, json) {
                        $(".tablepagados").fadeIn();
                    },
                    responsive: true,
                    ordering: false,
                    autoWidth: false,
                    data: data[1],
                    dataSrc: "",
                    lengthMenu: [10, 20, 50, 75],
                    bPaginate: true,
                    bProcessing: true,
                    columns: [
                        { data: "check" },
                        { data: "nombre" },
                        { data: "fecha" },
                        { data: "banco" },
                        { data: "fechapago" },
                        { data: "estatus" },
                        { data: "acciones" },
                    ],
                    createdRow: function (row, data, dataIndex) {
                        $(row).attr("data-id", data.id); // suponiendo que 'id' es el campo único
                    },
                });

                $(".check").rules("add", {
                    alMenosUnoSeleccionado: true,
                });
            },
            error: function (data) {
                var Toastr = main();
                Toastr.fire({
                    icon: "error",
                    title: data.responseJSON.message,
                });
            },
        });
    },
});
$("#uploadincidencias").validate({
    lang: "es",
    errorElement: "span",
    validClass: "is-valid",
    errorPlacement: function (error, element) {
        $(element).addClass("is-invalid text-sm");
        console.log(element);
        if (element.is('input[type="checkbox"]')) {
            var Toastr = main();
            Toastr.fire({
                icon: "error",
                title: error,
            });
        } else {
            element.closest(".form-group").append(error);
        }
        //
    },
    highlight: function (element, errorClass, validClass) {
        $(element).addClass("is-invalid text-sm");
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass("error is-invalid text-sm");
    },
    submitHandler: function (form) {
        form.submit();
    },
});

$("#changeincidencias").validate({
    lang: "es",
    errorElement: "span",
    validClass: "is-valid",
    errorPlacement: function (error, element) {
        $(element).addClass("is-invalid text-sm");
        console.log(element);
        if (element.is('input[type="checkbox"]')) {
            var Toastr = main();
            Toastr.fire({
                icon: "error",
                title: error,
            });
        } else {
            element.closest(".form-group").append(error);
        }
        //
    },
    highlight: function (element, errorClass, validClass) {
        $(element).addClass("is-invalid text-sm");
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass("error is-invalid text-sm");
    },
    submitHandler: function (form) {
        form.submit();
    },
});

$("#donwloadreportes").validate({
    lang: "es",
    errorElement: "span",
    validClass: "is-valid",
    errorPlacement: function (error, element) {
        $(element).addClass("is-invalid text-sm");
        console.log(element);
        if (element.is('input[type="checkbox"]')) {
            var Toastr = main();
            Toastr.fire({
                icon: "error",
                title: error,
            });
        } else {
            element.closest(".form-group").append(error);
        }
        //
    },
    highlight: function (element, errorClass, validClass) {
        $(element).addClass("is-invalid text-sm");
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass("error is-invalid text-sm");
    },
    submitHandler: function (form) {
        form.submit();
    },
});

///apartado de reportes comisiones
$("#area_trabajo_report").on("change", function () {
    var url = $(this).data("href");
    var data = { area: $(this).val(), _token: $("input[name=_token]").val() };
    $("#carteras_report").empty();
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: "json",
        success: function (data) {
            var value = "[TODOS]";
            $("#carteras_report").append(
                $("<option></option>").attr("value", "0").text(value)
            );
            for (i = 0; i < data.length; i++) {
                $("#carteras_report").append(
                    $("<option></option>")
                        .attr("value", data[i].id)
                        .text(data[i].nombre_cartera)
                );
            }
        },
        error: function () {
            var Toastr = main();
            Toastr.fire({
                icon: "error",
                title: "&nbsp; Opción Invalida.",
            });
        },
    });
});
$("#tipo_periodo_report").on("change", function () {
    ///siempre mantenemos lso botones desactivados
    var url = $(this).data("href");
    var tipo_periodo = $(this).val();

    var data = {
        tipo_periodo: tipo_periodo,
        mes: $("#mes_report").val(),
        _token: $("input[name=_token]").val(),
    };
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: "json",
        success: function (data) {
            switch (tipo_periodo) {
                case "Quincenal":
                    $("#corte_report").empty();
                    $("#corte_report").append(
                        $("<option></option>")
                            .attr("value", "0")
                            .text("[TODO EL MES]")
                    );

                    //el datatimepicjker se inicializa mediante la cantidad de meses segun el permisos otorgado
                    $("#mes_report").attr("disabled", false);

                    $.each(data.opcion_corte_show, function (key, value) {
                        $("#corte_report").append(
                            $("<option></option>")
                                .attr("value", key)
                                .text(value)
                                .prop("selected", false) // Marca como seleccionada la única opción
                        );
                    });
                    break;
                case "Semanal":
                    $("#corte_report").empty();
                    $("#corte_report").append(
                        $("<option></option>")
                            .attr("value", "0")
                            .text("[TODO EL MES]")
                    );

                    $("#mes_report").attr("disabled", false);

                    $.each(data.arreglo_semanal, function (key, value) {
                        $("#corte_report").append(
                            $("<option></option>")
                                .attr("value", key)
                                .text(value)
                        );
                    });
                    break;
            }
        },
        error: function (data) {
            var Toastr = main();
            Toastr.fire({
                icon: "error",
                title: data.responseJSON.message,
            });
        },
    });
});
$("#mes_report").on("blur", function () {
    ///siempre mantenemos lso botones desactivados
    var url = $(this).data("href");
    var tipo_periodo = $("#tipo_periodo_report").val();

    var data = {
        mes: $(this).val(),
        tipo_periodo: $("#tipo_periodo_report").val(),
        _token: $("input[name='_token']").val(),
        skill: $("#skill").val(),
    };

    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: "json",
        success: function (data) {
            switch (tipo_periodo) {
                case "Quincenal":
                    $("#corte_report").empty();
                    $("#corte_report").append(
                        $("<option></option>")
                            .attr("value", "0")
                            .text("[TODO EL MES]")
                    );

                    //el datatimepicjker se inicializa mediante la cantidad de meses segun el permisos otorgado
                    $("#mes_report").attr("disabled", false);

                    $.each(data.opcion_corte_show, function (key, value) {
                        $("#corte_report").append(
                            $("<option></option>")
                                .attr("value", key)
                                .text(value)
                                .prop("selected", false) // Marca como seleccionada la única opción
                        );
                    });
                    break;
                case "Semanal":
                    $("#corte_report").empty();
                    $("#corte_report").append(
                        $("<option></option>")
                            .attr("value", "0")
                            .text("[TODO EL MES]")
                    );
                    $.each(data.arreglo_semanal, function (key, value) {
                        $("#corte_report").append(
                            $("<option></option>")
                                .attr("value", key)
                                .text(value)
                        );
                    });
                    break;
            }
        },
        error: function () {
            var Toastr = main();
            Toastr.fire({
                icon: "error",
                title: data.responseJSON.message,
            });
        },
    });
});
///reporte incidencias
$("#showreporteincidencias").validate({
    lang: "es",
    errorElement: "span",
    validClass: "is-valid",
    errorPlacement: function (error, element) {
        error.addClass("is-invalid text-sm");
        element.closest(".form-group").append(error);
    },
    highlight: function (element, errorClass, validClass) {
        $(element).addClass("is-invalid text-sm");
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass("error is-invalid text-sm");
    },
    submitHandler: function (form) {
        event.preventDefault();
        $.ajax({
            type: form.method,
            url: form.action,
            data: $(form).serialize(),
            dataType: "json",
            success: function (data) {
                if ($.fn.dataTable.isDataTable("#datatable-incidencias")) {
                    $(".tableaudit").fadeOut();
                    $("#datatable-incidencias").DataTable().destroy();
                }
                $("#datatable-incidencias").DataTable({
                    initComplete: function (settings, json) {
                        $(".tableaudit").fadeIn();
                    },
                    responsive: true,
                    ordering: false,
                    autoWidth: false,
                    data: data,
                    dataSrc: "",
                    lengthMenu: [10, 20, 50, 75],
                    bPaginate: true,
                    bProcessing: true,
                    columns: [
                        { data: "nombrelote" },
                        { data: "periodo" },
                        { data: "banco" },
                        { data: "fechapago" },
                        { data: "estatus" },
                        { data: "acciones" },
                    ],
                    createdRow: function (row, data, dataIndex) {
                        $(row).attr("data-id", data.id); // suponiendo que 'id' es el campo único
                    },
                });
            },
            error: function (data) {
                var Toastr = main();
                Toastr.fire({
                    icon: "error",
                    title: data.responseJSON.message,
                });
            },
        });
    },
});
