document.addEventListener("DOMContentLoaded", function () {
    window.stepper = new Stepper(document.querySelector(".bs-stepper"));
});

$.validator.addMethod(
    "selectMultipleRequired",
    function (value, element) {
        $(element).closest(".select2").addClass("is-invalid");
        return $(element).val() !== null && $(element).val().length > 0;
    },
    "Debe seleccionar al menos una opción."
);

$.validator.addMethod(
    "notEqualTo",
    function (value, element, param) {
        return this.optional(element) || value != $(param).val();
    },
    "Este valor no puede ser igual al campo anterior."
);

var validation = (function () {
    var ficha = function () {
        $("#personal-form").validate({
            ignore: [],
            rules: {
                ctabancaria: {
                    remote: {
                        url: $("#ctabancaria").data("href"),
                        type: "post",
                        data: {
                            _token: $("input[name=_token]").val(),
                            cedula: $("#cedula_empleado").val(),
                        },
                    },
                },
                /* cedulatitular: {
                     equalTo: "La Cedula del titular debe ser igual a la cedula de la persona a registrar."
                 }*/
            },
            messages: {
                ctabancaria: {
                    remote: "La cuenta que a ingresado esta asignada a otro usuario. Ingrese una diferente.",
                },
                /* cedulatitular: {
                     equalTo: "La Cedula del titular debe ser igual a la cedula de la persona a registrar."
                 }*/
            },
            lang: "es",
            errorElement: "span",
            validClass: "is-valid",
            errorPlacement: function (error, element) {
                error.addClass("is-invalid text-sm");
                element.closest(".form-group").append(error);
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass("is-invalid text-sm");
                $(".step button").find("small.required").remove();
                var validatePane = $(
                    ".bs-stepper-content .content:has(.is-invalid)"
                ).each(function () {
                    var id = $(this).attr("id");
                    $(".step")
                        .find('button[aria-controls="' + id + '"]')
                        .find("span.bs-stepper-label")
                        .after(
                            ' <small class="required" style="color:red;"> <b>***</b></small>'
                        );
                });
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass("error is-invalid text-sm");
            },
            submitHandler: function (form) {
                var inputs = form.getElementsByTagName("input");
                for (var i = 0; i < inputs.length; i++) {
                    // Verificar si el input está deshabilitado
                    if (inputs[i].disabled) {
                        // Cambiar a readonly
                        inputs[i].disabled = false; // Primero habilitar el input
                        inputs[i].readOnly = true; // Luego establecerlo como readonly
                    }
                }
                var select = form.getElementsByTagName("select");
                for (var i = 0; i < select.length; i++) {
                    // Verificar si el input está deshabilitado
                    if (select[i].disabled) {
                        // Cambiar a readonly
                        select[i].disabled = false; // Primero habilitar el input
                        select[i].readOnly = true; // Luego establecerlo como readonly
                    }
                }

                var select = $("#ceco");
                var valor = select.val();

                // Si el select está vacío o tiene el valor por defecto
                if (valor.length === 0) {
                    select.addClass("is-invalid");
                    event.preventDefault();
                    return false; // Evita el envío del formulario
                } else {
                    select.removeClass("is-invalid");
                    form.submit();
                }
            },
        });
    };
    return {
        // public functions
        init: function () {
            ficha();
        },
    };
})();

$(document).ready(function () {
    validation.init();

    $(".dateter").datetimepicker({
        format: "DD/MM/YYYY",
    });

    if ($("#displayVacaciones").attr("style") !== "display:none;") {
        console.log("es visible");
        // Inicializar solo si no está ya inicializado
        if (!$(".vacacionesDateclass").data("datetimepicker")) {
            $(".vacacionesDateclass").datetimepicker({
                format: "DD/MM/YYYY",
                locale: "es", // para mostrar en español, si tienes el locale cargado
            });
        }
    } else {
        console.log(
            "no es visible",
            $("#displayVacaciones").is(":visible"),
            $("#displayVacaciones")
        );
        // Opcional: destruir el datetimepicker si el contenedor no está visible
        if ($(".vacacionesDateclass").data("datetimepicker")) {
            $(".vacacionesDateclass").datetimepicker("destroy");
        }
    }

    $(".input-number").on("input", function () {
        this.value = this.value.replace(/[^0-9]/g, "");
    });

    $(".time").datetimepicker({
        format: "hh:mm a",
    });

    $(".select2").select2({ theme: "bootstrap4" });

    $("#recontratar").bootstrapSwitch({
        offText: "NO",
        onText: "SI",
        ///se toma el valor del data en funcion si la persona esta en lista negra, si es true existe, si es false no existe
        state: $("#recontratar").data("state"),
    });

    if ($("#view_personal_form").length > 0) {
        $("#view_personal_form :input").prop("disabled", true);
        $("#view_personal_form :input[type='checkbox']").prop("disabled", true);
        $("#view_personal_form :button").prop("disabled", false);
    }

    function actualizarFechaFinal(maxDays, applyInput, contratoInput) {
        var selectedDate = contratoInput.val();
        if (!selectedDate) {
            applyInput.val("");
            return;
        }

        // Asumimos formato dd/mm/yyyy
        var parts = selectedDate.split("/");
        if (parts.length !== 3) {
            applyInput.val("Fecha inválida");
            return;
        }

        var day = parseInt(parts[0], 10);
        var month = parseInt(parts[1], 10) - 1; // Mes base 0
        var year = parseInt(parts[2], 10);

        var fecha = new Date(year, month, day);
        if (isNaN(fecha.getTime())) {
            applyInput.val("Fecha inválida");
            return;
        }

        var diasASumar = parseInt(maxDays, 10);
        if (isNaN(diasASumar)) diasASumar = 0;

        fecha.setDate(fecha.getDate() + diasASumar);

        // Formatear fecha dd/mm/yyyy
        var dd = ("0" + fecha.getDate()).slice(-2);
        var mm = ("0" + (fecha.getMonth() + 1)).slice(-2);
        var yyyy = fecha.getFullYear();

        var fechaFinalStr = dd + "/" + mm + "/" + yyyy;

        // Setear valor en input final
        applyInput.val(fechaFinalStr);

        // Inicializar datetimepicker en finalcontrato1 con maxDate = fechaFinal
        // Primero destruir si existe
        if (applyInput.data("DateTimePicker")) {
            applyInput.datetimepicker("destroy");
        }
        applyInput.removeAttr("disabled", "disabled");
        // Inicializar datetimepicker con maxDate
        applyInput.datetimepicker({
            format: "DD/MM/YYYY",
            minDate: fecha,
            maxDate: fecha,
        });
    }

    // Ejecutar cuando cambie la fecha en contrato1
    $("#contrato1").blur(function () {
        var selector = $(this);
        var applyInput = $("#finalcontrato1");

        // Destruir datetimepicker si existe
        var MaxDays = $("#maxmdayscontrato1").val() - 1;
        actualizarFechaFinal(MaxDays, applyInput, selector);
    });

    $("#contrato2").blur(function () {
        var selector = $(this);
        var applyInput = $("#finalcontrato2");
        var MaxDays =
            $("#maxmonthcontrato2").val() !== ""
                ? $("#maxmonthcontrato2").val() - 1
                : "";
        actualizarFechaFinal(MaxDays, applyInput, selector);

        if (selector.val() === applyInput.val()) {
            applyInput.removeClass("is-valid");
            applyInput.addClass("is-invalid");
        } else {
            applyInput.removeClass("is-invalid");
            applyInput.addClass("is-valid");
        }
    });

    $("#maxmonthcontrato2").change(function () {
        var selector = $("#contrato2");
        var applyInput = $("#finalcontrato2");
        var MaxDays = $(this).val() !== "" ? $(this).val() - 1 : "";
        actualizarFechaFinal(MaxDays, applyInput, selector);

        if (selector.val() === applyInput.val()) {
            applyInput.removeClass("is-valid");
            applyInput.addClass("is-invalid");
        } else {
            applyInput.removeClass("is-invalid");
            applyInput.addClass("is-valid");
        }
    });
});
///validacion de la cedula para bblacklist | registrada o no
$("#cedula_empleado").blur(function () {
    var url = $(this).data("href");
    var data = {
        cedula_empleado: $(this).val(),
        _token: $("input[name=_token]").val(),
    };
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: "json",
        success: function (data) {
            if (data.blacklist === false && data.response === true) {
                $(".validateNext").removeAttr("disabled", "disabled");
                $("#cedula_empleado").addClass("is-valid");
                return true;
            }

            if (data.blacklist === false) {
                $("#cedula_empleado").addClass("error is-invalid");
                $(".validateNext").attr("disabled", "disabled");
                var Toastr = main();
                Toastr.fire({
                    icon: "warning",
                    html: data.message,
                });
                return false;
            }

            if (data.response === false) {
                //$('#cedula_empleado').addClass('error is-invalid');
                //$('.validateNext').attr('disabled', 'disabled');
                var Toastr = main();
                Toastr.fire({
                    icon: "info",
                    html: data.message,
                });
                return false;
            }
        },
    });
});

/////para mostrar el selector del transporte
$("input[data-bootstrap-switch]").each(function () {
    $(this).bootstrapSwitch({
        offText: "NO",
        onText: "SI",
    });
});

$("#alimentacion").on("switchChange.bootstrapSwitch", function (e, state) {
    $("input[name=alimentacion]").val(state ? "1" : "0");
});

$("#integral").on("switchChange.bootstrapSwitch", function (e, state) {
    $("input[name=integral]").val(state ? "1" : "0");
});

$("#activetransporte").on("switchChange.bootstrapSwitch", function (e, data) {
    if ($("#activetransporte").bootstrapSwitch("state") === true) {
        $(".displaytransporte").fadeIn();
        $("#zona_transporte_id").attr("required", "required");
        $("#htransporte").attr("required", "required");
    } else {
        $(".displaytransporte").fadeOut();
        $("#zona_transporte_id").removeAttr("required", "required");
        $("#htransporte").removeAttr("required", "required");
    }
});

$("#useractive").on("switchChange.bootstrapSwitch", function (e, data) {
    if ($("#useractive").bootstrapSwitch("state") === true) {
        $("#displayusers").fadeIn();
        $("#name").val($("#pnombre").val() + " " + $("#papellido").val());
        $("#username").val($("#cedula_empleado").val());
        $("#useremail").val(
            $("#pnombre").val() +
                "." +
                $("#papellido").val() +
                "@directagroup.net"
        );
        $("#name").attr("required", "required");
        $("#username").attr("required", "required");
        $("#roles").attr("required", "required");
        $("#email").attr("required", "required");
    } else {
        $("#displayusers").fadeOut();
        $("#name").val(null);
        $("#username").val(null);
        $("#useremail").val(null);
        $("#username").removeAttr("required", "required");
        $("#name").removeAttr("required", "required");
        $("#roles").removeAttr("required", "required");
        $("#useremail").removeAttr("required", "required");
    }
});

$("#resetPasswordAsisOp").bootstrapSwitch({ offText: "NO", onText: "SI" });

$("#cjefeinmediato").on("change", function () {
    var url = $(this).data("href");
    var data = {
        idCargo: $(this).val(),
        _token: $("input[name=_token]").val(),
    };

    $("#jinmediato").empty();
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: "json",
        success: function (data) {
            var value = "[SELECCIONE]";
            $("#jinmediato").append(
                $("<option></option>").attr("value", "").text(value)
            );
            for (i = 0; i < data.length; i++) {
                $("#jinmediato").append(
                    $("<option></option>")
                        .attr("value", data[i].id)
                        .text(
                            data[i].primer_nombre +
                                " " +
                                data[i].primer_apellido
                        )
                );
            }
            var value = "N/A";
            $("#jinmediato").append(
                $("<option></option>").attr("value", "0").text(value)
            );
        },
    });
});

$("#cjefeinmediato2").on("change", function () {
    var url = $(this).data("href");
    var data = {
        idCargo: $(this).val(),
        _token: $("input[name=_token]").val(),
    };

    $("#jinmediato2").empty();
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: "json",
        success: function (data) {
            var value = "[SELECCIONE]";
            $("#jinmediato2").append(
                $("<option></option>").attr("value", "").text(value)
            );
            for (i = 0; i < data.length; i++) {
                $("#jinmediato2").append(
                    $("<option></option>")
                        .attr("value", data[i].id)
                        .text(
                            data[i].primer_nombre +
                                " " +
                                data[i].primer_apellido
                        )
                );
            }
            var value = "N/A";
            $("#jinmediato2").append(
                $("<option></option>").attr("value", "0").text(value)
            );
        },
    });
});

$("#areatrabajo").on("change", function () {
    var url = $(this).data("href");
    var data = { area: $(this).val(), _token: $("input[name=_token]").val() };
    $("#cartera").empty();
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: "json",
        success: function (data) {
            var value = "[SELECCIONE]";
            $("#cartera").append(
                $("<option></option>").attr("value", "").text(value)
            );
            for (i = 0; i < data.length; i++) {
                $("#cartera").append(
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

$("#cartera").on("change", function () {
    var url = $(this).data("href");
    var data = {
        cartera: $(this).val(),
        _token: $("input[name=_token]").val(),
    };
    $("#skill").empty();

    if ($(this).val() == 20 || $(this).val() == 89) {
        $("#usuario_crm_porta_display").show();
        $("#usuario_crm_portabilidad").attr("required", "required");
    } else {
        $("#usuario_crm_porta_display").hide();
        $("#usuario_crm_portabilidad").removeAttr("required", "required");
    }
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
                        .attr("data-codcecos", data[i].cod_ceco)
                        .attr("value", data[i].id)
                        .text(data[i].nombre_skill)
                );
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

///recarga el selector de cecos
$("#skill").on("change", function () {
    var skill = $(this).val();
    var url = $(this).data("href");
    var _token = $("input[name='_token']").val();
    $("#ceco").empty();
    $.ajax({
        type: "POST",
        url: url,
        data: { skill: skill, _token: _token },
        dataType: "json",
        success: function (data) {
            if (data.length > 1) {
                for (i = 0; i < data.length; i++) {
                    $("#ceco").append(
                        $("<option></option>")
                            .attr("value", data[i].id)
                            .text(data[i].nombre_ceco)
                    );
                    $("#porcentaje").val("");
                }
            } else {
                $("#ceco").append(
                    $("<option></option>")
                        .attr("value", data.id)
                        .text(data.nombre_ceco)
                );
                $("#ceco option").val(data.id).attr("selected", "selected");
                $("#ceco").attr("disabled");
                $("#porcentaje").val("");
            }
            $("#ceco").removeAttr("disabled");
            $("#ceco").rules("add", {
                selectMultipleRequired: true,
            });
            $("#porcentaje").val("");
        },
    });
});

///aplica para los porcentajes del ceco
$("#skill").on("change", function () {
    if (
        $("#skill option:selected").data("codcecos") === "00000" &&
        $(this).data("actporcentaje") === true
    ) {
        $(".porcentajececo").show();
        $("#porcentaje").attr("required", "required");
    } else {
        $(".porcentajececo").hide();
        $("#porcentaje").removeAttr("required", "required");
    }
});

//// aplica para los porcentajes de ceco
$("#porcentaje").on("input", function () {
    // Obtener el valor actual del input
    let valor = $(this).val();
    // Reemplazar espacios por comas
    valor = valor.replace(/ /g, ",");
    // Actualizar el valor del input
    $(this).val(valor);
});

$("#recontratar").on("switchChange.bootstrapSwitch", function (e, data) {
    if ($("#recontratar").bootstrapSwitch("state") === false) {
        $("#norecontratardisplay").fadeIn();
        $("#norecontratar").attr("required", "required");
    } else {
        $("#norecontratardisplay").fadeOut();
        $("#norecontratar").removeAttr("required", "required");
    }
});

$("#status").on("change", function () {
    switch ($(this).val()) {
        case "2":
        case "6":
            //activa el bloque de egresos
            HandleEgresosStatus();
            ///desactivba el bloque de vacaciones
            HidenParcial_Vacaciones();
            break;
        case "8":
            //activa bloque de vacaciones
            handleVacationStatus();
            //desactiva el bloque de egresos
            HidenParcial_Egreso();
            break;
        default:
            //parcial del egreso
            HidenParcial_Egreso();
            //parcial de vacaciones
            HidenParcial_Vacaciones();
            break;
    }
});

function HidenParcial_Vacaciones() {
    $("#displayVacaciones").fadeOut();
    if ($("#vacaciones_init").data("DateTimePicker")) {
        $("#vacaciones_init").data("DateTimePicker").destroy();
    }
    $("#vacaciones_init").val("");
    $("#vacaciones_end").val("");
    $("#vacaciones_init").removeAttr("required", "required");
    $("#vacaciones_end").removeAttr("required", "required");
}

function HidenParcial_Egreso() {
    $("#divegreso").fadeOut();
    $("#fegreso").val("");
    $("#motivoegreso").val("");
    $("#observaciones_egreso").val("");
    $("#fegreso").removeAttr("required", "required");
    $("#motivoegreso").removeAttr("required", "required");
    $("#observaciones_egreso").removeAttr("required", "required");
}
//logica del bloque de egresos
function HandleEgresosStatus() {
    $("#divegreso").fadeIn();
    $("#fegreso").attr("required", "required");
    $("#motivoegreso").attr("required", "required");
    $("#observaciones_egreso").attr("required", "required");

    if ($("#fegreso").data("DateTimePicker")) {
        $("#fegreso").data("DateTimePicker").destroy();
    }

    $("#fegreso").datetimepicker({
        locale: "es",
        format: "DD/MM/YYYY",
        maxDate: moment(),
    });

    $("#recontratar").bootstrapSwitch("state", true);
    $("#useractive").bootstrapSwitch("state", false);
}
// Function to handle Vacation status logic
function handleVacationStatus() {
    $("#displayVacaciones").fadeIn();
    $("#divegreso").fadeOut();

    $("#vacaciones_init").rules("add", {
        required: true,
    });

    $("#vacaciones_end").rules("add", {
        required: true,
        notEqualTo: "#vacaciones_init",
    });

    if ($(".vacacionesDateclass").data("DateTimePicker")) {
        $(".vacacionesDateclass").data("DateTimePicker").destroy();
    }

    $(".vacacionesDateclass").datetimepicker({
        locale: "es",
        format: "DD/MM/YYYY",
        minDate: moment(),
    });
    HidenParcial_Egreso();
}

$("#fegreso").blur(function () {
    Swal.fire({
        title: "!Atención!",
        text: "Una vez actualizado el expediente no podra realizar ningun tipo de cambios. Sera Bloquedo de acuerdo al marco legal por 90 Días ¿Esta De acuerdo?",
        icon: "info",
        showCancelButton: true,
        showConfirmButton: true,
        cancelButtonText: "Cancelar",
        confirmButtonText: "Confirmar",
    }).then((result) => {
        // Se agrega el método .then()
        if (result.dismiss == Swal.DismissReason.cancel) {
            // Aquí se valida si se presionó 'Cancelar'
            $("#fegreso").val(""); // Se limpia el input 'fegreso'
        }
    });
});

$("#sede").on("change", function () {
    var sede = $(this).val();
    if (sede == "STV" || sede == "ATENAS") {
        $(".region-estado").show();
        $("#region").prop("required", true);
        $("#estado").prop("required", true);
        $("#ciudad").prop("required", true);
    } else {
        $(".region-estado").hide();
        $("#region").prop("required", false);
        $("#estado").prop("required", false);
        $("#ciudad").prop("required", false);
    }
});

$("#region").on("change", function () {
    var region = $(this).val();
    var url = $(this).data("href");
    var _token = $("input[name='_token']").val();
    $.ajax({
        type: "POST",
        url: url,
        data: { region: region, _token: _token },
        dataType: "json",
        success: function (data) {
            $("#estado").empty();
            $("#estado").append(
                $("<option></option>").attr("value", "").text("[SELECCIONE]")
            );
            for (i = 0; i < data.length; i++) {
                $("#estado").append(
                    $("<option></option>")
                        .attr("value", data[i].id)
                        .text(data[i].nombre_estado)
                );
            }
        },
    });
});

$("#estado").on("change", function () {
    var estado_id = $(this).val();
    var url = $(this).data("href");
    var _token = $("input[name='_token']").val();
    $.ajax({
        type: "POST",
        url: url,
        data: { estado_id: estado_id, _token: _token },
        dataType: "json",
        success: function (data) {
            $("#ciudad").empty();
            $("#ciudad").append(
                $("<option></option>").attr("value", "").text("[SELECCIONE]")
            );
            for (i = 0; i < data.length; i++) {
                $("#ciudad").append(
                    $("<option></option>")
                        .attr("value", data[i].id)
                        .text(data[i].ciudad)
                );
            }
        },
    });
});

function finalcontratos(fecha, summes, input) {
    var fechaselect = fecha.split("/");
    var fechafinal = new Date(fechaselect[2], fechaselect[1], fechaselect[0]);
    return fechafinal.setMonth(fechafinal.getMonth() + summes);
}
///preview imagen select
var loadFile = function (event) {
    var image = document.getElementById("previewimg");
    image.src = URL.createObjectURL(event.target.files[0]);
};
