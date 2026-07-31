if ($(".daterange").length !== 0) {
    $(".daterange").daterangepicker({
        locale: {
            format: "DD/MM/YYYY",
        },
        maxDate: moment(),
    });
}

if ($(".daterangedialy").length !== 0) {
    $(".daterangedialy").datetimepicker({
        format: "DD/MM/YYYY",
        minDate: moment().add(-8, "days"),
        maxDate: moment(),
    });
}

if ($(".select2").length !== 0) {
    $(".select2").select2({ theme: "bootstrap4" });
}

var validation = (function () {
    var asistenciaCursos = function () {
        $("#FormAsistencia").validate({
            ignore: [],
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
                Swal.fire({
                    title: "¡Atención!",
                    html: "¿Está seguro de registrar esta asistencia?, Si existe algun problema comuniquelo a Talento Humano.",
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
    };
    return {
        init: function () {
            asistenciaCursos();
        },
    };
})();

////PREBUSQUEDA DE ASISTENCIAS
$("#asistenciasearchcursos").validate({
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
                $("#display_asistencia").empty();
                $("#display_asistencia").append(data.printhtml).fadeIn();
                $(".inputasistencia").select2({ theme: "bootstrap4" });
                $("#fechaasistencia").val($("#fecha").val());
                $("#datatable-asistencias").DataTable({
                    responsive: true,
                    ordering: false,
                    autoWidth: false,
                    //"lengthMenu": [10, 20, 50, 75],
                    bPaginate: false,
                    bProcessing: true,
                });

                validation.init();
            },
            error: function (data) {
                $("#display_asistencia").empty();
                var Toastr = main();
                Toastr.fire({
                    icon: "error",
                    title: data.responseJSON.message,
                });
            },
        });
    },
});

///parte de adminsitracion y eliminacion
$("#area_trabajo").on("change", function () {
    var area = $(this).val();
    var url = $(this).data("href");
    var _token = $("input[name='_token']").val();
    $.ajax({
        type: "POST",
        url: url,
        data: { area: area, _token: _token },
        dataType: "json",
        success: function (data) {
            if ($("#FormIndicadores").length == 1) {
                $("#carteras").empty();
                $("#carteras").append(
                    $("<option></option>")
                        .attr("value", "")
                        .text("[SELECCIONE]")
                );
            } else {
                $("#carteras").empty();
                $("#carteras").append(
                    $("<option></option>").attr("value", "").text("[TODOS]")
                );
            }

            for (i = 0; i < data.length; i++) {
                $("#carteras").append(
                    $("<option></option>")
                        .attr("value", data[i].id)
                        .text(data[i].nombre_cartera)
                );
            }
        },
    });
});

$("#carteras").on("change", function () {
    var cartera = $(this).val();
    var url = $(this).data("href");
    var _token = $("input[name='_token']").val();
    $.ajax({
        type: "POST",
        url: url,
        data: { cartera: cartera, _token: _token },
        dataType: "json",
        success: function (data) {
            if ($("#FormIndicadores").length == 1) {
                $("#skill").empty();
                var activeOptnionAll = true;
            } else {
                $("#skill").empty();
                $("#skill").append(
                    $("<option></option>").attr("value", "").text("[TODOS]")
                );
            }

            for (i = 0; i < data.length; i++) {
                $("#skill").append(
                    $("<option></option>")
                        .attr("value", data[i].id)
                        .text(data[i].nombre_skill)
                );
            }

            if (activeOptnionAll) {
                $("#skill option").prop("selected", true).trigger("change");
            }
        },
    });
});

$("#formadministracion").validate({
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
                if ($.fn.dataTable.isDataTable("#datatable-dir_personal")) {
                    $("#tablelistado").fadeOut();
                    $("#datatable-dir_personal").DataTable().destroy();
                }

                $("#datatable-dir_personal").DataTable({
                    initComplete: function (settings, json) {
                        $("#tablelistado").fadeIn();
                    },
                    responsive: true,
                    ordering: false,
                    autoWidth: false,
                    data: data,
                    lengthMenu: [10, 20, 50, 75],
                    bPaginate: true,
                    bProcessing: true,
                    columns: [
                        { mData: "cedula" },
                        { mData: "nombres" },
                        { mData: "area_trabajo" },
                        { mData: "cartera" },
                        { mData: "skill" },
                        { mData: "cargo" },
                        { mData: "dia" },
                        { mData: "prefijo" },
                        { mData: "acciones" },
                    ],
                });
            },
            error: function (data) {
                $("#display_asistencia").empty();
                var Toastr = main();
                Toastr.fire({
                    icon: "error",
                    title: data.responseJSON.message,
                });
            },
        });
    },
});

$("#asistencias_edit").validate({
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
        form.submit();
    },
});

$("#formreportes").validate({
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
        form.submit();
    },
});

function checkAsistencia() {
    if ($(".inputasistencia").length === 0) {
        var Toastr = main();
        Toastr.fire({
            icon: "info",
            title: "&nbsp; Debes ubicar primero el personal a quien se le tomara asistencia",
        });
    } else {
        $(".inputasistencia").val("A").trigger("change");
    }
}

////eliminar asistencias
$("#formadministraciondeleted").validate({
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
                if ($.fn.dataTable.isDataTable("#datatable-delete")) {
                    $("#tablelistado").fadeOut();
                    $("#datatable-delete").DataTable().destroy();
                }

                $("#datatable-delete").DataTable({
                    initComplete: function (settings, json) {
                        $("#tablelistado").fadeIn();
                    },
                    responsive: true,
                    ordering: false,
                    autoWidth: false,
                    data: data,
                    lengthMenu: [10, 20, 50, 75],
                    bPaginate: true,
                    bProcessing: true,
                    columns: [
                        { mData: "cedula" },
                        { mData: "nombres" },
                        { mData: "area_trabajo" },
                        { mData: "cartera" },
                        { mData: "skill" },
                        { mData: "dia" },
                        { mData: "asistencias" },
                        { mData: "acciones" },
                    ],
                });
            },
            error: function (data) {
                $("#display_asistencia").empty();
                var Toastr = main();
                Toastr.fire({
                    icon: "error",
                    title: data.responseJSON.message,
                });
            },
        });
    },
});

function ValidateDestroyAsistencia(key) {
    var deletedasistencias = (function () {
        var EliminarAsistencias = function () {
            $("#formulariodeleted" + key).validate({
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
                    Swal.fire({
                        title: "¡Atención!",
                        html: "¿Está seguro en eliminar estas asistencias?, Debera informar al Lider Inmediato para volver a cargarlas.",
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
        };
        return {
            init: function () {
                EliminarAsistencias();
            },
        };
    })();

    deletedasistencias.init();
}

$("#FormIndicadores").validate({
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
        form.submit();
    },
});
