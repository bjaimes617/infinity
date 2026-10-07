$.validator.addMethod(
    "alMenosUnoSeleccionado",
    function (value, element) {
        return $(".check:checked").length > 0;
    },
    "Seleccione al menos un elemento."
);

$(function () {
    $(document).on("input", ".input-number", function () {
        this.value = this.value.replace(/[^0-9]/g, "");
    });

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

            $("#periodo").empty();
            $("#periodo").append(
                $("<option></option>").attr("value", "").text("[CORTE]")
            );
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
    var cartera = [];
    ///enviamos array de carteras si es distribuido
    if ($(this).val() == 53) {
        $("#carteras option").each(function () {
            if ($(this).val() !== "") {
                cartera.push($(this).val());
            }
        });
    } else {
        cartera = $(this).val();
    }

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
            $('button[type="submit"]').prop("disabled", false);
        },
        error: function (data) {
            var Toastr = main();
            Toastr.fire({
                icon: "error",
                title: data.responseJSON.message,
            });
            $("#periodo").empty();
            $("#periodo").append(
                $("<option></option>").attr("value", "").text("[CORTE]")
            );

            $('button[type="submit"]').prop("disabled", true);
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

            $("#periodo").empty();
            $("#periodo").append(
                $("<option></option>").attr("value", "").text("[CORTE]")
            );
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
        error: function (data) {
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
    if (url !== undefined) {
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
    }
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

$("#gastos").on("click", function () {
    selectmes = $(this).data("type");
    url = $(this).data("href");

    switch (selectmes) {
        case "auditoria":
            var mes = $("#auditmes").val();
            break;
        case "procesar":
            var mes = $("#paymentmes").val();
            break;
    }

    var data = {
        _token: $('input[name="_token"]').val(),
        mes: mes,
    };

    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: "json",
        success: function (response) {
            let tableDataQuincenal = [];
            let tableDataSemanal = [];

            // Procesar Quincenal (response[0])
            $.each(response[0], function (gerenciaId, gerenciaData) {
                let gerenciaName = gerenciaData.gerencia;
                $.each(gerenciaData, function (skillId, skillData) {
                    if (skillId === "gerencia") return;

                    tableDataQuincenal.push({
                        gerencia: gerenciaName,
                        skill: skillData.skill,
                        pq1:
                            skillData.Quincenal && skillData.Quincenal[1]
                                ? "$" +
                                  parseFloat(skillData.Quincenal[1]).toFixed(2)
                                : "$ 0.0",
                        p1:
                            skillData.Quincenal && skillData.Quincenal[1]
                                ? parseFloat(skillData.Quincenal[1])
                                : 0,
                        p2:
                            skillData.Quincenal && skillData.Quincenal[2]
                                ? parseFloat(skillData.Quincenal[2])
                                : 0,
                        pq2:
                            skillData.Quincenal && skillData.Quincenal[2]
                                ? "$" +
                                  parseFloat(skillData.Quincenal[2]).toFixed(2)
                                : "$ 0.0",
                        totalskill:
                            "$" + parseFloat(skillData.total).toFixed(2),
                        total: parseFloat(skillData.total || 0),

                        total_num: parseFloat(skillData.total || 0),
                    });
                });
            });

            // Procesar Semanal (response[1])
            $.each(response[1], function (gerenciaId, gerenciaData) {
                let gerenciaName = gerenciaData.gerencia;
                $.each(gerenciaData, function (skillId, skillData) {
                    if (skillId === "gerencia") return;

                    tableDataSemanal.push({
                        gerencia: gerenciaName,
                        skill: skillData.skill,
                        pt1:
                            skillData.Semanal && skillData.Semanal[1]
                                ? parseFloat(skillData.Semanal[1])
                                : 0,
                        p1:
                            skillData.Semanal && skillData.Semanal[1]
                                ? "$ " +
                                  parseFloat(skillData.Semanal[1]).toFixed(2)
                                : "$ 0.00",
                        pt2:
                            skillData.Semanal && skillData.Semanal[2]
                                ? parseFloat(skillData.Semanal[2])
                                : 0,
                        p2:
                            skillData.Semanal && skillData.Semanal[2]
                                ? "$ " +
                                  parseFloat(skillData.Semanal[2]).toFixed(2)
                                : "$ 0.00",
                        pt3:
                            skillData.Semanal && skillData.Semanal[3]
                                ? parseFloat(skillData.Semanal[3])
                                : 0,
                        p3:
                            skillData.Semanal && skillData.Semanal[3]
                                ? "$ " +
                                  parseFloat(skillData.Semanal[3]).toFixed(2)
                                : "$ 0.00",
                        pt4:
                            skillData.Semanal && skillData.Semanal[4]
                                ? parseFloat(skillData.Semanal[4])
                                : 0,
                        p4:
                            skillData.Semanal && skillData.Semanal[4]
                                ? "$ " +
                                  parseFloat(skillData.Semanal[4]).toFixed(2)
                                : "$ 0.00",
                        pt5:
                            skillData.Semanal && skillData.Semanal[5]
                                ? parseFloat(skillData.Semanal[5])
                                : 0,
                        p5:
                            skillData.Semanal && skillData.Semanal[5]
                                ? "$ " +
                                  parseFloat(skillData.Semanal[5]).toFixed(2)
                                : "$ 0.00",
                        total: skillData.total
                            ? "$ " + parseFloat(skillData.total).toFixed(2)
                            : "$ 0.00",
                        total_num: parseFloat(skillData.total || 0),
                    });
                });
            });

            if ($.fn.dataTable.isDataTable("#datatable-quincenal")) {
                $("#datatable-quincenal").DataTable().destroy();
            }
            if ($.fn.dataTable.isDataTable("#datatable-semanal")) {
                $("#datatable-semanal").DataTable().destroy();
            }

            $("#gastos-modal .modal-body").empty();

            let theadQuincenal = `
                <h5 class="text-primary">Costo Estimado Quincenal</h5>
                <table id="datatable-quincenal" class="table compact table-sm text-sm table-bordered table-striped text-center w-100">
                    <thead>
                        <tr>
                            <th>Gerencia</th>
                            <th>Segmento/Skill</th>
                            <th>1ra Quincena</th>
                            <th>2da Quincena</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                    <tfoot>
                        <tr>
                            <th colspan="2" style="text-align:right">Total Costo Estimado por quincena:</th>    
                            <th id="totalquincena1"></th>
                            <th id="totalquincena2"></th>                                            
                            <th id="totalgasto"></th>
                        </tr>
                    </tfoot>
                </table>
                <hr class="my-4">
                <h5 class="mb-3 text-primary">Costo Estimado Semanal</h5>
                <table id="datatable-semanal" class="table compact table-sm text-sm table-bordered table-striped text-center w-100">
                    <thead>
                        <tr>
                            <th>Gerencia</th>
                            <th>Segmento/Skill</th>
                            <th>Semana 1</th>
                            <th>Semana 2</th>
                            <th>Semana 3</th>
                            <th>Semana 4</th>
                            <th>Semana 5</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody></tbody>                    
                    <tfoot>
                        <tr>
                            <th colspan="2" style="text-align:right">Total Costo por Semana:</th>
                            <th id="totalsem1"></th>
                            <th id="totalsem2"></th>
                            <th id="totalsem3"></th>
                            <th id="totalsem4"></th>
                            <th id="totalsem5"></th>
                            <th id="totalgastosem"></th>
                        </tr>
                    </tfoot>
                </table>
            `;
            $("#gastos-modal .modal-body").html(theadQuincenal);

            // Tabla Quincenal
            $("#datatable-quincenal").DataTable({
                data: tableDataQuincenal,
                responsive: true,
                ordering: true,
                autoWidth: false,
                bPaginate: false,
                columns: [
                    { data: "gerencia", visible: false },
                    { data: "skill" },
                    { data: "pq1" },
                    { data: "pq2" },
                    { data: "totalskill" },
                ],
                order: [[0, "asc"]],
                drawCallback: function (settings) {
                    var api = this.api();
                    var rows = api.rows({ page: "current" }).nodes();
                    var last = null;

                    api.column(0, { page: "current" })
                        .data()
                        .each(function (group, i) {
                            if (last !== group) {
                                $(rows)
                                    .eq(i)
                                    .before(
                                        '<tr class="group"><td colspan="4" style="background-color:#1B3155; color:white; font-weight:bold; text-align:left;">' +
                                            group +
                                            "</td></tr>"
                                    );
                                last = group;
                            }
                        });
                },
                footerCallback: function (row, data, start, end, display) {
                    var api = this.api();
                    var sumTotal = 0;
                    var sumTotalp1 = 0;
                    var sumTotalp2 = 0;
                    api.rows({ search: "applied" }).every(function () {
                        sumTotal += this.data().total_num || 0;
                        sumTotalp1 += this.data().p1 || 0;
                        sumTotalp2 += this.data().p2 || 0;
                    });
                    $("#totalgasto").html("$" + sumTotal.toFixed(2));
                    $("#totalquincena1").html("$" + sumTotalp1.toFixed(2));
                    $("#totalquincena2").html("$" + sumTotalp2.toFixed(2));
                },
            });

            // Tabla Semanal
            $("#datatable-semanal").DataTable({
                data: tableDataSemanal,
                responsive: true,
                ordering: true,
                autoWidth: false,
                bPaginate: false,
                columns: [
                    { data: "gerencia", visible: false },
                    { data: "skill" },
                    { data: "p1" },
                    { data: "p2" },
                    { data: "p3" },
                    { data: "p4" },
                    { data: "p5" },
                    { data: "total" },
                ],
                order: [[0, "asc"]],
                drawCallback: function (settings) {
                    var api = this.api();
                    var rows = api.rows({ page: "current" }).nodes();
                    var last = null;
                    api.column(0, { page: "current" })
                        .data()
                        .each(function (group, i) {
                            if (last !== group) {
                                $(rows)
                                    .eq(i)
                                    .before(
                                        '<tr class="group"><td colspan="7" style="background-color:#1B3155; color:white; font-weight:bold; text-align:left;">' +
                                            group +
                                            "</td></tr>"
                                    );
                                last = group;
                            }
                        });
                },
                footerCallback: function (row, data, start, end, display) {
                    var api = this.api();
                    var semana1 = 0;
                    var semana2 = 0;
                    var semana3 = 0;
                    var semana4 = 0;
                    var semana5 = 0;
                    var total_num = 0;
                    api.rows({ search: "applied" }).every(function () {
                        semana1 += this.data().pt1 || 0;
                        semana2 += this.data().pt2 || 0;
                        semana3 += this.data().pt3 || 0;
                        semana4 += this.data().pt4 || 0;
                        semana5 += this.data().pt5 || 0;
                        total_num += this.data().total_num || 0;
                    });
                    $("#totalsem1").html("$ " + semana1.toFixed(2));
                    $("#totalsem2").html("$ " + semana2.toFixed(2));
                    $("#totalsem3").html("$ " + semana3.toFixed(2));
                    $("#totalsem4").html("$ " + semana4.toFixed(2));
                    $("#totalsem5").html("$ " + semana5.toFixed(2));
                    $("#totalgastosem").html("$ " + total_num.toFixed(2));
                },
            });

            $("#gastos-modal").modal("show");
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
