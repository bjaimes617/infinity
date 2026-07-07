$(document).ready(function () {
    $.validator.addMethod(
        "selectMultipleRequired",
        function (value, element) {
            $(element).closest(".select2").addClass("is-invalid");
            return $(element).val() !== null && $(element).val().length > 0;
        },
        "Debe seleccionar al menos una opción."
    );

    if ($(".select2").length != 0) {
        $(".select2").select2({
            placeholder: "[Seleccione una o mas Opciones]",
            minimumInputLengthopcion: 0,
            minimumResultsForSearch: 0,
        });
        // con esto forzamos que el selector no se envie vacio al ser un selector multiple o individual
        $(".select2").rules("add", {
            selectMultipleRequired: true,
        });
    }
});
/////--- Validaciones para creacion
$("#gerencias_create").validate({
    lang: "es",
    errorElement: "span",
    errorPlacement: function (error, element) {
        error.addClass("is-invalid text-sm");
        element.closest(".form-group").append(error);
    },
    highlight: function (element, errorClass, validClass) {
        $(element).addClass("is-invalid");
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass("is-invalid");
    },
    submitHandler: function (form) {
        //event.preventDefault();
        form.submit();
    },
});

$("#unidades_create").validate({
    lang: "es",
    errorElement: "span",
    errorPlacement: function (error, element) {
        error.addClass("is-invalid text-sm");
        element.closest(".form-group").append(error);
    },
    highlight: function (element, errorClass, validClass) {
        $(element).addClass("is-invalid");
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass("is-invalid");
    },
    submitHandler: function (form) {
        //event.preventDefault();
        form.submit();
    },
});

$("#skill_create").validate({
    lang: "es",
    errorElement: "span",
    errorPlacement: function (error, element) {
        error.addClass("is-invalid text-sm");
        element.closest(".form-group").append(error);
    },
    highlight: function (element, errorClass, validClass) {
        $(element).addClass("is-invalid");
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass("is-invalid");
    },
    submitHandler: function (form) {
        //event.preventDefault();
        form.submit();
    },
});
/////--- Validaciones para actualizacion
$("#gerencias_update").validate({
    lang: "es",
    errorElement: "span",
    errorPlacement: function (error, element) {
        error.addClass("is-invalid text-sm");
        element.closest(".form-group").append(error);
    },
    highlight: function (element, errorClass, validClass) {
        $(element).addClass("is-invalid");
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass("is-invalid");
    },
    submitHandler: function (form) {
        event.preventDefault();
        Swal.fire({
            title: "<b>¿Esta de Acuerdo con los Cambios realizados? </b>",
            showDenyButton: false,
            showCancelButton: true,
            confirmButtonText: "Procesar",
            denyButtonText: "Regresar",
            cancelButtonText: "Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    },
});

$("#unidades_update").validate({
    lang: "es",
    errorElement: "span",
    errorPlacement: function (error, element) {
        error.addClass("is-invalid text-sm");
        element.closest(".form-group").append(error);
    },
    highlight: function (element, errorClass, validClass) {
        $(element).addClass("is-invalid");
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass("is-invalid");
    },
    submitHandler: function (form) {
        event.preventDefault();
        Swal.fire({
            title: "<b>¿Esta de Acuerdo con los Cambios realizados? </b>",
            html: "Recuerda que al cambiar el estatus a <b>INACTIVO</b>, La Unidad/Campaña no estara disponible y tampoco estara disponible los Segmentos/skills asociados.",
            showDenyButton: false,
            showCancelButton: true,
            confirmButtonText: "Procesar",
            denyButtonText: "Regresar",
            cancelButtonText: "Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    },
});

$("#skill_updates").validate({
    lang: "es",
    errorElement: "span",
    errorPlacement: function (error, element) {
        error.addClass("is-invalid text-sm");
        element.closest(".form-group").append(error);
    },
    highlight: function (element, errorClass, validClass) {
        $(element).addClass("is-invalid");
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass("is-invalid");
    },
    submitHandler: function (form) {
        event.preventDefault();
        Swal.fire({
            title: "<b>¿Esta de Acuerdo con los Cambios realizados? </b>",
            html: "La modificación de los Datos del Skill, afectara toda la estructura del sistema.",
            showDenyButton: false,
            showCancelButton: true,
            confirmButtonText: "Procesar",
            denyButtonText: "Regresar",
            cancelButtonText: "Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    },
});

$('select[name="area_trabajo"]').on("change", function () {
    var data = {
        area: $(this).val(),
        _token: $('input[name="_token"]').val(),
    };
    var url = $(this).data("href");
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: "json",
        success: function (data) {
            if ($("#gerenciaedit").is(":visible")) {
                $("#gerenciaedit").empty();
                $("#gerenciaedit").val(
                    $('select[name="area_trabajo"]')
                        .find("option:selected")
                        .data("nombre")
                );
                ///activa lso botones de envio
                $("#buttongerencia").removeAttr("disabled");
                $("#buttongerenciadelete").removeAttr("disabled");
            } else {
                $('select[name="carteras"]').empty();
                $('select[name="carteras"]').append(
                    $("<option></option>")
                        .attr("value", "")
                        .text("[Seleccione]")
                );
                for (i = 0; i < data.length; i++) {
                    $('select[name="carteras"]').append(
                        $("<option></option>")
                            .attr("data-nombrecartera", data[i].nombre_cartera)
                            .attr("data-estatus_id", data[i].estatus_id)
                            .attr("value", data[i].id)
                            .text(data[i].nombre_cartera)
                    );
                }
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

$('select[name="carteras"]').on("change", function () {
    var cartera = $(this).val();
    var url = $(this).data("href");
    var _token = $("input[name='_token']").val();
    $.ajax({
        type: "POST",
        url: url,
        data: { cartera: cartera, _token: _token },
        dataType: "json",
        success: function (data) {
            $("#skill").empty();
            $("#codceco").empty();
            $("#skill").append(
                $("<option></option>").attr("value", "").text("[Seleccione]")
            );
            for (i = 0; i < data.length; i++) {
                $("#skill").append(
                    $("<option></option>")
                        .attr("data-codceco", data[i].cod_ceco)
                        .attr("data-nombre", data[i].nombre_skill)
                        .attr("data-nombceco", data[i].nombre_ceco)
                        .attr("data-active", data[i].active)
                        .attr("value", data[i].id)
                        .text(data[i].nombre_skill)
                );
            }
        },
    });
});

$("#carteras").on("change", function () {
    if ($("#carteraedit").is(":visible")) {
        $("#carteraedit").empty();
        ///compruebo si el campo de edicion de carteras esta en la vista, si no, imprimo el selector de skills
        /// index de personal y ajustes de estructura, gerencias
        $("#carteraedit").val(
            $('select[name="carteras"] option:selected').data("nombrecartera")
        );
        $("#estatuscartera").val(
            $('select[name="carteras"] option:selected').data("estatus_id")
        );
        ///activa los botones de envio
        $("#buttonunidad").removeAttr("disabled");
        $("#buttonunidaddelete").removeAttr("disabled");
    }
});

$("#skill").on("change", function () {
    $("#codceco").val(
        $('select[name="skill"] option:selected').data("codceco")
    );
    $("#nombceco").val(
        $('select[name="skill"] option:selected').data("nombceco")
    );
    $("#editskills").val(
        $('select[name="skill"] option:selected').data("nombre")
    );
    $("#estatusskill").val(
        $('select[name="skill"] option:selected').data("active")
    );
    ///activa los botones para enviar el formulario///
    $("#buttonskill").removeAttr("disabled");
    $("#buttonskilldelete").removeAttr("disabled");

    var url = $(this).data("href");
    var data = {
        skill_id: $(this).val(),
        _token: $("input[name='_token']").val(),
    };
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: "json",
        success: function (data) {
            $("#tipo_periodo").val(data.periodo);
            switch (data.periodo) {
                case "Semanal":
                    $("#semanal").show();

                    $("#quincenal").hide();
                    $("#dias").attr("required", true);
                    $("#dias").removeAttr("disabled");
                    $("#desde1").attr("disabled", "disabled");
                    $("#desde1").removeAttr("required");
                    $("#desde1").val("");
                    $("#hasta1").attr("disabled", "disabled");
                    $("#hasta1").removeAttr("required");
                    $("#hasta1").val("");
                    $("#desde2").attr("disabled", "disabled");
                    $("#desde2").removeAttr("required");
                    $("#desde2").val("");
                    $("#hasta2").attr("disabled", "disabled");
                    $("#hasta2").removeAttr("required");
                    $("#hasta2").val("");

                    for (i = 0; i < data.dias.length; i++) {
                        $("#dias option[value=" + data.dias[i] + "]").attr(
                            "selected",
                            true
                        );
                    }

                    $("#dias").select2({
                        placeholder: "[Seleccione una o mas Opciones]",
                    });
                    break;
                case "Quincenal":
                    $("#quincenal").show();

                    $("#semanal").hide();
                    $("#dias").val(null).trigger("change");
                    $("#dias").removeAttr("required");
                    $("#dias").attr("disabled", "disabled");
                    $("#desde1").removeAttr("disabled").val(data.rango.desde1);
                    $("#hasta1").removeAttr("disabled").val(data.rango.hasta1);
                    $("#desde2").removeAttr("disabled").val(data.rango.desde2);
                    $("#hasta2").removeAttr("disabled").val(data.rango.hasta2);
                    $("#desde1").attr("required", true);
                    $("#hasta1").attr("required", true);
                    $("#desde2").attr("required", true);
                    $("#hasta2").attr("required", true);
                    break;
                case "Mixto":
                    $("#semanal").show();
                    $("#quincenal").show();
                    $("#dias").attr("required", true);
                    $("#dias").removeAttr("disabled");

                    $("#dias").select2({
                        placeholder: "[Seleccione una o mas Opciones]",
                    });
                    for (i = 0; i < data.dias.length; i++) {
                        $("#dias option[value=" + data.dias[i] + "]")
                            .attr("selected", true)
                            .trigger("change");
                    }
                    // $("#dias").val(null).trigger('change');
                    $("#desde1").removeAttr("disabled").val(data.rango.desde1);
                    $("#hasta1").removeAttr("disabled").val(data.rango.hasta1);
                    $("#desde2").removeAttr("disabled").val(data.rango.desde2);
                    $("#hasta2").removeAttr("disabled").val(data.rango.hasta2);
                    $("#desde1").attr("required", true);
                    $("#hasta1").attr("required", true);
                    $("#desde2").attr("required", true);
                    $("#hasta2").attr("required", true);
                    break;
            }

            $("#tipo_formato").val(null).trigger("change");
            data.type_format.forEach(function (item) {
                $("#tipo_formato option[value=" + item + "]")
                    .prop("selected", true)
                    .trigger("change");
            });
        },
    });
});

//// activa los displays de la vista de estructuras areas, unidades, skills
function displays(segmentos) {
    $(".hidendivs").hide();
    $("#" + segmentos).fadeIn();
}
///Para la creacion y edicion de los periodos de pago al crear los skills
$("#tipo_periodo").on("change", function () {
    switch ($(this).val()) {
        case "Semanal":
            $("#semanal").show();
            $("#quincenal").hide();

            $("#dias").attr("required", true);
            $("#dias").removeAttr("disabled");

            $("#desde1").attr("disabled", "disabled");
            $("#desde1").removeAttr("required");
            $("#desde1").val("");

            $("#hasta1").attr("disabled", "disabled");
            $("#hasta1").removeAttr("required");
            $("#hasta1").val("");

            $("#desde2").attr("disabled", "disabled");
            $("#desde2").removeAttr("required");
            $("#desde2").val("");

            $("#hasta2").attr("disabled", "disabled");
            $("#hasta2").removeAttr("required");
            $("#hasta2").val("");

            $("#dias").val(null).trigger("change");
            $("#dias").select2({
                placeholder: "[Seleccione una o mas Opciones]",
            });
            break;
        case "Quincenal":
            $("#quincenal").show();

            $("#semanal").hide();
            $("#dias").val(null).trigger("change");
            $("#dias").removeAttr("required");
            $("#dias").attr("disabled", "disabled");

            $("#desde1").removeAttr("disabled");
            $("#hasta1").removeAttr("disabled");
            $("#desde2").removeAttr("disabled");
            $("#hasta2").removeAttr("disabled");

            $("#desde1").attr("required", true);
            $("#hasta1").attr("required", true);
            $("#desde2").attr("required", true);
            $("#hasta2").attr("required", true);

            break;
        case "Mixto":
            $("#semanal").show();
            $("#quincenal").show();

            $("#dias").attr("required", true);
            $("#dias").removeAttr("disabled");
            $("#dias").val(null).trigger("change");
            $("#dias").select2({
                placeholder: "[Seleccione una o mas Opciones]",
            });

            $("#desde1").removeAttr("disabled");
            $("#hasta1").removeAttr("disabled");
            $("#desde2").removeAttr("disabled");
            $("#hasta2").removeAttr("disabled");

            $("#desde1").attr("required", true);
            $("#hasta1").attr("required", true);
            $("#desde2").attr("required", true);
            $("#hasta2").attr("required", true);
            break;
    }
});

function activemodaldelete(modal) {
    $("#sendformdelete").empty();
    $("#sendformdelete").val(modal);
    $("#delgerencia").val($("#area_trabajo").val());
    $("#delunidad").val($("#carteras").val());
    $("#delskill").val($("#skill").val());

    switch (modal) {
        case "gerencia":
            var titulo = "Eliminación de Estructura principal";
            var mensaje =
                "<b>¿Esta de Acuerdo con eliminar Esta Gerencia? </b><br> La unidad/campaña y el segmento/skill seran eliminadas en su totalidad. Esta acción no puede ser reversada.";
            break;
        case "unidad":
            var titulo =
                "Eliminación de Estructura principal - Unidades/Campañas";
            var mensaje =
                "<b>¿Esta de Acuerdo con eliminar la Unidad/Campaña seleccionada?</b><br> Los Segmentos/skill  asociados seran eliminados en su totalidad. Esta acción no puede ser reversada.";
            break;
        case "skill":
            var titulo =
                "Eliminación de Estructura principal - Segmentos/skills";
            var mensaje =
                "<b>¿Esta de Acuerdo con eliminar el Segmento/skill seleccionado?</b><br> Los Segmentos/skill  asociados seran eliminados en su totalidad. Esta acción no puede ser reversada.";
            break;
    }
    Swal.fire({
        title: titulo,
        html: mensaje,
        showDenyButton: false,
        showCancelButton: true,
        confirmButtonText: "Procesar",
        denyButtonText: "Regresar",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            $("#senddeleted").submit();
        }
    });
}
