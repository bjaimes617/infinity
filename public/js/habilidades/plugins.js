var validation = function () {
    var CrearEvaluacion = function () {
        $('#evaluacion_create').validate({
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

                $('.step button').find('small.required').remove();

                var validatePane = $('.bs-stepper-content .content:has(.is-invalid)').each(function () {
                    var id = $(this).attr('id');
                    $('.step').find('button[aria-controls="' + id + '"]').find('span.bs-stepper-label').after(' <small class="required" style="color:red;"> <b>***</b></small>');
                });
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('error is-invalid text-sm');
            },
            submitHandler: function (form) {
                Swal.fire({
                    title: '!Atención!',
                    text: 'Se procedera a registrar la evaluacion de Habilidades. ¿Esta de acuerdo con los valores ingresados?',
                    icon: 'info',
                    showCancelButton: true,
                    showConfirmButton: true,
                    cancelButtonText: 'Cancelar',
                    confirmButtonText: 'Confirmar',
                }).then((result) => {     // Se agrega el método .then()
                    if (result.isConfirmed) {
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
    var UpdateEvaluacion = function () {
        $('#evaluacion_update').validate({
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

                $('.step button').find('small.required').remove();

                var validatePane = $('.bs-stepper-content .content:has(.is-invalid)').each(function () {
                    var id = $(this).attr('id');
                    $('.step').find('button[aria-controls="' + id + '"]').find('span.bs-stepper-label').after(' <small class="required" style="color:red;"> <b>***</b></small>');
                });
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('error is-invalid text-sm');
            },
            submitHandler: function (form) {
                Swal.fire({
                    title: '!Atención!',
                    text: 'Se procedera a actualizar la evaluacion de Habilidades. ¿Esta de acuerdo con los valores ingresados?',
                    icon: 'info',
                    showCancelButton: true,
                    showConfirmButton: true,
                    cancelButtonText: 'Cancelar',
                    confirmButtonText: 'Confirmar',
                }).then((result) => {     // Se agrega el método .then()
                    if (result.isConfirmed) {
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
        Crear: function () {
            CrearEvaluacion();
        },
        Update: function () {
            UpdateEvaluacion();
        }
    };
}();

if ($('.bs-stepper').length !== 0) {
    document.addEventListener('DOMContentLoaded', function () {
        window.stepper = new Stepper(document.querySelector('.bs-stepper'));
    });
}

$(document).ready(function () {

    if ($('.select2').length !== 0) {
        $('.select2').select2({
            placeholder: "Seleccione una o mas Opciones",
            theme: 'bootstrap4'
        });
    }
    // Agregar una opción adicional
    var nuevaOpcion = new Option("[TODAS]", "TODAS");
    $('#gerencia').prepend(nuevaOpcion);

    if ($('.daterange').length !== 0) {
        $('.daterange').daterangepicker({
            // Formato específico para mostrar
            locale: {
                format: 'DD/MM/YYYY' // Cambia esto al formato que necesites
            },
        });
    }

    if ($('.table').length != 0) {
        $(".table").DataTable({
            "lengthMenu": [10, 20, 15, 30, 50, 60],
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "ordering": true,
            //"order":[8,"ASC"]
            //"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        });
    }

    if ($('.estatusevaluacion').length != 0) {
        $('.estatusevaluacion').bootstrapSwitch({
            offText: "Inactiva",
            onText: "Activa",
            ///se toma el valor del data en funcion si la persona esta en lista negra, si es true existe, si es false no existe          
        });
    }

    ///bloqueara los inputs del formulario de actualizacion
    if ($('#evaluacion_update').length != 0) {
        if ($('#totalresultados') > 0) {
            $("#evaluacion_update :input").prop("disabled", true);
            $("#evaluacion_update :select").prop("disabled", true);
            $("#evaluacion_update :input[type='checkbox']").prop("disabled", true);
            $("#evaluacion_update :submit").prop("disabled", false);
        }

        var suma = 0;

        // Seleccionar todos los inputs cuyo id empieza con 'valor_pregunta'
        $('input[id^="valor_categoria"]').each(function () {
            // Obtener el valor del campo actual, convertirlo a número y sumar
            let valor = parseFloat($(this).val()) || 0; // Si no es un número, usar 0
            suma += valor;
        });

        if ($('#distribucion').val() === 2) {
            var total_sum = suma.toFixed(3);
        } else {
            var total_sum = Math.round(suma);
        }
        $('#total_sum').val(total_sum);

        validation.Update();
    }
});

$(document).on('switchChange.bootstrapSwitch', '.estatusevaluacion', function (event, state) {

    var previousState = !state;
    // Determinamos que switch cambió y actuamos el modal
    //$('#' + $(this).prop('id')).bootstrapSwitch('state', !state, true);
    switch ($('#' + $(this).prop('id')).bootstrapSwitch('state')) {
        case true:
            var data = {estatus: 1, _token: $("input[name='_token']").val()};
            var confirmacion = '¿Está de acuerdo en ejecutar esta acción? Una vez activado, no se podrá modificar sus datos.';
            break;
        case false:
            var data = {estatus: 0, _token: $("input[name='_token']").val()};
            var confirmacion = '¿Está de acuerdo en ejecutar esta acción? Una vez Desactivado, el mismo no estara disponible.'
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
                        title: data,
                    });
                }, error: function (data) {
                    var Toastr = main();
                    Toastr.fire({
                        icon: 'error',
                        title: " " + data.responseJSON.message,
                    });
                }
            });
        } else {
            // Restaurar el estado anterior
            $('#' + $(this).prop('id')).bootstrapSwitch('state', previousState, true);
        }
    });
});

$('#gerencia').on('change', function () {

    var select = $(this);
    var selectedValue = select.val();
    if (selectedValue && selectedValue.includes('TODAS')) {
        // Seleccionar todas las opciones excepto "TODAS"
        select.find('option:not([value="TODAS"])').prop('selected', true);
        select.find('option[value="TODAS"]').prop('selected', false);
        select.trigger('change'); // Actualizar select2
    } else {
        // Deseleccionar "todos" si se deselecciona o se selecciona otra opción
        select.find('option[value="TODAS"]').prop('selected', false);
    }

    var url = $(this).data('href');
    var data = {area: $(this).val(), autorize: true, _token: $("input[name=_token]").val(), };

    $('#cartera').empty();

    var nuevaOpcion = new Option("[TODAS]", "TODAS");
    $('#cartera').prepend(nuevaOpcion);
    $.ajax({
        type: 'POST',
        url: url,
        data: data,
        dataType: 'json',
        success: function (data) {
            for (i = 0; i < data.length; i++)
            {
                $('#cartera').append($("<option></option>").attr("value", data[i].id).text(data[i].nombre_cartera));
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

$('#cartera').on('change', function () {
    var url = $(this).data('href');
    var data = {cartera: $(this).val(), autorize: true, _token: $("input[name=_token]").val(), };

    var select = $(this);
    var selectedValue = select.val();
    if (selectedValue && selectedValue.includes('TODAS')) {
        // Seleccionar todas las opciones excepto "TODAS"
        select.find('option:not([value="TODAS"])').prop('selected', true);
        select.find('option[value="TODAS"]').prop('selected', false);
        select.trigger('change'); // Actualizar select2
    } else {
        // Deseleccionar "todos" si se deselecciona o se selecciona otra opción
        select.find('option[value="TODAS"]').prop('selected', false);
    }

    $('#skill').empty();
    var nuevaOpcion = new Option("[TODAS]", "TODAS");

    $('#skill').prepend(nuevaOpcion);
    $.ajax({
        type: 'POST',
        url: url,
        data: data,
        dataType: 'json',
        success: function (data)
        {
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

    var select = $(this);
    var selectedValue = select.val();
    if (selectedValue && selectedValue.includes('TODAS')) {
        // Seleccionar todas las opciones excepto "TODAS"
        select.find('option:not([value="TODAS"])').prop('selected', true);
        select.find('option[value="TODAS"]').prop('selected', false);
        select.trigger('change'); // Actualizar select2
    } else {
        // Deseleccionar "todos" si se deselecciona o se selecciona otra opción
        select.find('option[value="TODAS"]').prop('selected', false);
    }
});

$('#valor_max').on('change', function () {
    var valor_max = $(this).val();
    $('#valor_max2').val(valor_max);
    $('#valor_max3').val(valor_max);
});

//PERMITE AGREGAR UNA NUEVA CATEGORIA
function AddCategoryEvaluacion() {

    if ($('#valor_max').val() === '') {
        var Toastr = main();
        Toastr.fire({
            icon: 'info',
            title: "Indique primero el valor maximo de la Evaluacion.",
        });
    } else {
        var cantidad, form;
        cantidad = parseInt($('#add-category').data('conteo'));
        let contador = parseInt($('#totalcategorias').val());
        let contador_true = parseInt($('#totalcategorias_true').val());
        var distribucion = $('#distribucion').val();
        var valor_base = $('.valor-base').val();
        var suma = 0;

        // Seleccionar todos los inputs cuyo id empieza con 'valor_max'
        $('input[id^="valor_categoria"]').each(function () {
            // Obtener el valor del campo actual, convertirlo a número y sumar
            let valor = parseFloat($(this).val()) || 0; // Si no es un número, usar 0
            suma += valor;
        });

        //SI LA DISTRIBUCION ES MANUAL NO REDONDEAMOS LA SUMA TOTAL
        if (distribucion == 2) {
            var total_sum = suma;
        } else {
            var total_sum = Math.round(suma);
        }
        $('#total_sum').val(total_sum);
        var sumatoria = $('#total_sum').val();
        //SI LA SUMA TOTAl DEL PORCENTAJE DE LAS CATEGORIAS ES MAYOR O IGUAL AL VALOR MAXIMO DE LA EVALUACION NO SE DEBEN PERMITIR AGREGAR MAS CATEGORIAS EN EL CASO DE MANUAL
        if ((parseFloat(sumatoria) >= parseFloat(valor_base)) && distribucion === 2) {
            var Toastr = main();
            Toastr.fire({
                icon: 'info',
                title: "No se pueden agregar mas categorias",
            });
        } else {
            // Incrementar el contador
            contador++;
            contador_true++;
            // Actualizar el valor del input
            $('#totalcategorias').val(contador);
            $('#totalcategorias_true').val(contador_true);
            var contentToMove = $("#copycontent .row").clone();
            contentToMove.find('label[for="label1"]').text('Nombre de Categoria');
            contentToMove.find('label[for="label1"]').attr('for', 'nombre_categoria' + contador); // Actualizar el atributo 'for' de las etiquetas        
            contentToMove.find('input[id="label1"]').attr('id', 'nombre_categoria' + contador); // Actualizar el atributo 'id' de los inputs
            contentToMove.find('input[name="label1"]').attr('name', 'nombre_categoria' + contador); // Actualizar el atributo 'name' de los inputs 
            contentToMove.find('input[name="nombre_categoria' + contador + ']').prop('required', true);

            contentToMove.find('label[for="label2"]').text('Valor');
            contentToMove.find('label[for="label2"]').attr('for', 'valor_categoria' + contador);
            contentToMove.find('input[id="label2"]').attr('id', 'valor_categoria' + contador);
            contentToMove.find('input[name="label2"]').attr('name', 'valor_categoria' + contador);
            contentToMove.find('input[name="valor_categoria' + contador + ']').prop('required', true);

            contentToMove.find('label[for="labeld"]').text('Descripción');
            contentToMove.find('label[for="labeld"]').attr('for', 'descripcion' + contador);
            contentToMove.find('input[id="labeld"]').attr('id', 'descripcion' + contador);
            contentToMove.find('input[name="labeld"]').attr('name', 'descripcion' + contador);
            contentToMove.find('input[name="descripcion' + contador + ']').prop('required', true);

            contentToMove.find('input[id="nombre_categoria' + contador + '"]').addClass('conteoinputs');
            contentToMove.find('input[id="valor_categoria' + contador + '"]').addClass('conteoinputs');
            contentToMove.find('button[id="deleted"]').attr('onclick', 'Rowdeleted(' + contador + ')');
            contentToMove.attr('id', 'category-' + contador);

            $('#pastecontent').append(contentToMove); // Agregar el contenido clonado al nuevo div
            $('#add-category').data('conteo', contador);
            $('#nuevo' + contador).val(true);

            validation.Crear();
        }
        //SI LA DISTRIBUCION ES AUTOMATICA AL AGREGAR UNA CATEGORIA SE COLOCAN COMO SOLO LECTURA Y RECALCULA
        if (distribucion == 1) {
            $('#valor_categoria' + contador).attr('disabled', 'disabled');
            $('.valor-categoria').attr('disabled', 'disabled');
            calcularResultados(valor_base);
        } else {
            if (parseFloat(sumatoria) != parseFloat(valor_base)) {
                $('#valor_categoria' + contador).val("");
            }
        }
        //SI LA CANTIDAD DE CATEGORIAS ES 0 DESACTIVAMOS EL BOTON SIGUIENTE, DE LO CONTRARIO LO ACTIVAMOS
        let cantidad_parametros_true = parseInt($('#totalparametros_true').val());
        let cantidad_categorias_true = parseInt($('#totalcategorias_true').val());

        cantidad_parametros_true < 1 || cantidad_categorias_true < 1 ?
                $('#nextparametro').attr('disabled', true) :
                $('#nextparametro').removeAttr('disabled', false);

    }
}
function Rowdeleted(value) {
    var suma = 0;
    const categoryId = value;
    var dis = $('#distribucion').val();
    //SI LA DISTRIBUCION ES AUTOMATICA CALCULAMOS LA SUMATORIA ANTES DE ELIMINAR LA CATEGORIA
    if (dis === 1) {
        // Seleccionar todos los inputs cuyo id empieza con 'valor_max'
        $('input[id^="valor_categoria"]').each(function () {
            // Obtener el valor del campo actual, convertirlo a número y sumar
            let valor = parseFloat($(this).val()) || 0; // Si no es un número, usar 0
            suma += valor;
        });
        var total_sum = Math.round(suma);
        $('#total_sum').val(total_sum);
    }
    $(`#category-${categoryId}`).remove();
    var valor_base = $('.valor-base').val();
    var total = $("#totalcategorias_true").val();
    var total_cat = total - 1;
    $("#totalcategorias_true").val(total_cat);

    let cantidad_parametros_true = parseInt($('#totalparametros_true').val());
    let cantidad_categorias_true = parseInt($('#totalcategorias_true').val());
    //SI LA CANTIDAD DE PARAMETROS ES 0 DESACTIVAMOS EL BOTON SIGUIENTE, DE LO CONTRARIO LO ACTIVAMOS
    if (cantidad_parametros_true < 1 || cantidad_categorias_true < 1) {
        $('#next').attr("disabled", "disabled");
    } else {
        $('#next').removeAttr("disabled");
    }
    //SI LA DISTRIBUCION ES AUTOMATICA RECALCULAMOS
    if (dis === 1) {
        calcularResultados(valor_base);
    }
    //SI LA DISTRIBUCION ES MANUAL CALCULAMOS LA SUMATORIA NUEVAMENTE CON LOS CAMPOS QUE QUEDARON VISIBLES
    if (dis === 2) {
        // Seleccionar todos los inputs cuyo id empieza con 'valor_pregunta'
        $('input[id^="valor_categoria"]').each(function () {
            // Obtener el valor del campo actual, convertirlo a número y sumar
            let valor = parseFloat($(this).val()) || 0; // Si no es un número, usar 0
            suma += valor;
        });
        var total_sum = suma;
        $('#total_sum').val(parseFloat(total_sum.toFixed(3)));
        //SI LA SUMATORIA TOTAL DEL VALOR DE LA CAT ES MAYOR AL VALOR MAXIMO DE LA EVAL DESACTIVAMOS EL BOTON DE GUARDADO
        if (parseFloat(total_sum) > parseFloat(valor_base)) {
            $('#guardar_evaluacion').attr("disabled", "disabled");
        } else {
            //DE LO CONTRARIO ACTIVAMOS EL BOTON
            $('#guardar_evaluacion').removeAttr("disabled");
        }
        //SI FALTAN PUNTOS O PORCENTAJES EN LA EVALUACION, TAMBIEN DESACTIVAMOS EL BOTON
        if (parseFloat(total_sum) < parseFloat(valor_base)) {
            $('#guardar_evaluacion').attr("disabled", "disabled");
        }
    }

}
//FUNCION QUE PERMITE COLOCAR LOS VALORES DE LAS CATEGORIAS AUTOMATICO O MANUAL
function distribuir() {
    var dis = $('#distribucion').val();
    var valor_base = $('.valor-base').val();
    var suma = 0;
    //SI LA DISTRIBUCION ES AUTOMATICA LOS CAMPOS DE LAS CATEGORIAS PASAN A SOLO LECTURA Y SE HACE EL CALCULO, EL CAMPO OCULTO TOTAL SUM QUEDA INUTILIZABLE
    if (dis == 1) {
        $('.valor-categoria').attr('readonly', 'readonly');
        $('#total_sum').val(0);
        calcularResultados(valor_base);
        $('#guardar_evaluacion').removeAttr("disabled");

    } else {
        //DE LO CONTRARIO SI ES MANUAL SE QUITA EL ATRIBUTO DE SOLO LECTURA Y REALIZAMOS CALCULOS PARA TOTAL SUM
        // Seleccionar todos los inputs cuyo id empieza con 'valor_pregunta'
        $('input[id^="valor_categoria"]').each(function () {
            // Obtener el valor del campo actual, convertirlo a número y sumar
            let valor = parseFloat($(this).val()) || 0; // Si no es un número, usar 0
            suma += valor;
        });
        var total_sum = suma;
        $('#total_sum').val(total_sum);
        $('.valor-categoria').removeAttr('readonly');
        if (parseFloat(total_sum) > parseFloat(valor_base)) {
            $('#guardar_evaluacion').attr("disabled", "disabled");
        } else {
            $('#guardar_evaluacion').removeAttr("disabled");
        }
        if (parseFloat(total_sum) < parseFloat(valor_base)) {
            $('#guardar_evaluacion').attr("disabled", "disabled");
        }
    }

}
//FUNCION QUE RECALCULA LA SUMATORIA AL ESCRIBIR EN LOS CAMPOS DE LOS VALORES DE LAS CATEGORIAS
$('.valor-categoria').on('input', function () {
    var suma = 0;
    var valor_base = $('.valor-base').val();
    // Seleccionar todos los inputs cuyo id empieza con 'valor_categoria'
    $('input[id^="valor_categoria"]').each(function () {
        // Obtener el valor del campo actual, convertirlo a número y sumar
        let valor = parseFloat($(this).val()) || 0; // Si no es un número, usar 0
        suma += valor;
    });

    var total_sum = suma;
    $('#total_sum').val(total_sum);
    if (parseFloat(total_sum) > parseFloat(valor_base)) {
        $('#guardar_evaluacion').attr("disabled", "disabled");
    } else {
        $('#guardar_evaluacion').removeAttr("disabled");
    }

    if (parseFloat(total_sum) < parseFloat(valor_base)) {
        $('#guardar_evaluacion').attr("disabled", "disabled");
    }

});

function calcularResultados(value) {
    const valorBase = parseFloat(value) || 0;
    const cantidadCamposAdicionales = $('.valor-categoria').length - 1;
    const resultadoDividido = cantidadCamposAdicionales > 0 ? valorBase / cantidadCamposAdicionales : 0;
    $('.valor-categoria').val(resultadoDividido.toFixed(3));

}
function sumar() {
    var suma = 0;
    var valor_base = $('.valor-base').val();
    // Seleccionar todos los inputs cuyo id empieza con 'valor_categoria'
    $('input[id^="valor_categoria"]').each(function () {
        // Obtener el valor del campo actual, convertirlo a número y sumar
        let valor = parseFloat($(this).val()) || 0; // Si no es un número, usar 0
        suma += valor;
    });
    //console.log(suma, "suma", parseFloat(suma));
    //var total_sum = Math.round(suma);
    $('#total_sum').val(parseFloat(suma.toFixed(3)));
    //console.log(parseFloat(suma) > parseFloat(valor_base), parseFloat(suma), parseFloat(valor_base));

    if (parseFloat(suma.toFixed(3)) > parseFloat(valor_base)) {
        $('#guardar_evaluacion').attr("disabled", "disabled");
    } else {

        $('#guardar_evaluacion').removeAttr("disabled");
    }
    if (parseFloat(suma.toFixed(3)) < parseFloat(valor_base)) {
        $('#guardar_evaluacion').attr("disabled", "disabled");
    }

}

//PERMITE AGFREGAR UN PARAMETRO
function add_parameter() {
    var cantidad;
    cantidad = parseInt($('#add-parameter').data('conteo-par'));
    let contador = parseInt($('#totalparametros').val());
    let contador_true = parseInt($('#totalparametros_true').val());
    // Incrementar el contador
    contador++;
    contador_true++;
    // Actualizar el valor del input
    $('#totalparametros').val(contador);
    $('#totalparametros_true').val(contador_true);

    var contentToMove = $("#copycontent_parameter .row").clone();
    contentToMove.find('label[for="label3"]').text('Nombre de Parámetro');
    contentToMove.find('label[for="label3"]').attr('for', 'nombre_parametro' + contador); // Actualizar el atributo 'for' de las etiquetas        
    contentToMove.find('input[id="label3"]').attr('id', 'nombre_parametro' + contador); // Actualizar el atributo 'id' de los inputs
    contentToMove.find('input[name="label3"]').attr('name', 'nombre_parametro' + contador); // Actualizar el atributo 'name' de los inputs 

    contentToMove.find('label[for="label4"]').text('Desde');
    contentToMove.find('label[for="label4"]').attr('for', 'desde' + contador);
    contentToMove.find('input[id="label4"]').attr('id', 'desde' + contador);
    contentToMove.find('input[name="label4"]').attr('name', 'desde' + contador);

    contentToMove.find('label[for="label5"]').text('Hasta');
    contentToMove.find('label[for="label5"]').attr('for', 'hasta' + contador);
    contentToMove.find('input[id="label5"]').attr('id', 'hasta' + contador);
    contentToMove.find('input[name="label5"]').attr('name', 'hasta' + contador);

    contentToMove.find('label[for="labelcolor"]').text('Color');
    contentToMove.find('label[for="labelcolor"]').attr('for', 'hex' + contador);
    contentToMove.find('input[id="labelcolor"]').attr('id', 'hex' + contador);
    contentToMove.find('input[name="labelcolor"]').attr('name', 'hex' + contador);

    contentToMove.find('input[id="nombre_parametro' + contador + '"]').addClass('conteoinputs');
    contentToMove.find('input[id="desde' + contador + '"]').addClass('conteoinputs');
    contentToMove.find('input[id="hasta' + contador + '"]').addClass('conteoinputs');
    contentToMove.find('button[id="deleted_parameter"]').attr('onclick', 'Rowdeleted_parameter(' + contador + ')');
    contentToMove.attr('id', 'parameter-' + contador);
//console.log(contentToMove.find('button[id="deleted"]'));
    $('#pastecontent_parameters').append(contentToMove); // Agregar el contenido clonado al nuevo div
    $('#add-parameter').data('conteo-par', contador);

    let cantidad_parametros_true = parseInt($('#totalparametros_true').val());
    let cantidad_categorias_true = parseInt($('#totalcategorias_true').val());
    console.log(cantidad_parametros_true, cantidad_categorias_true);
    //SI LA CANTIDAD DE PARAMETROS ES 0 DESACTIVAMOS EL BOTON SIGUIENTE, DE LO CONTRARIO LO ACTIVAMOS  

    validation.Crear();

    cantidad_parametros_true < 1 || cantidad_categorias_true < 1 ?
            $('#nextparametro').attr('disabled', true) :
            $('#nextparametro').removeAttr('disabled', false);
}
function Rowdeleted_parameter(value) {
    const parameterId = value;
    $(`#parameter-${parameterId}`).remove();
    var total = $("#totalparametros_true").val();
    var total_par = total - 1;
    $("#totalparametros_true").val(total_par);
    let cantidad_parametros_true = parseInt($('#totalparametros_true').val());
    let cantidad_categorias_true = parseInt($('#totalcategorias_true').val());

}

function add_range() {
    var cantidad;
    cantidad = parseInt($('#add-range').data('conteo-range'));
    let contador = parseInt($('#totalrangos').val());
    let contador_true = parseInt($('#totalrangos_true').val());
    // Incrementar el contador
    contador++;
    contador_true++;
    // Actualizar el valor del input
    $('#totalrangos').val(contador);
    $('#totalrangos_true').val(contador_true);

    var contentToMove = $("#copycontent_range .row").clone();
    contentToMove.find('label[for="label6"]').text('Descripción');
    contentToMove.find('label[for="label6"]').attr('for', 'descripcion_rango' + contador); // Actualizar el atributo 'for' de las etiquetas        
    contentToMove.find('input[id="label6"]').attr('id', 'descripcion_rango' + contador); // Actualizar el atributo 'id' de los inputs
    contentToMove.find('input[name="label6"]').attr('name', 'descripcion_rango' + contador); // Actualizar el atributo 'name' de los inputs 

    contentToMove.find('label[for="label7"]').text('Desde');
    contentToMove.find('label[for="label7"]').attr('for', 'rango_desde' + contador);
    contentToMove.find('input[id="label7"]').attr('id', 'rango_desde' + contador);
    contentToMove.find('input[name="label7"]').attr('name', 'rango_desde' + contador);

    contentToMove.find('label[for="label8"]').text('Hasta');
    contentToMove.find('label[for="label8"]').attr('for', 'rango_hasta' + contador);
    contentToMove.find('input[id="label8"]').attr('id', 'rango_hasta' + contador);
    contentToMove.find('input[name="label8"]').attr('name', 'rango_hasta' + contador);

    contentToMove.find('textarea[id="descripcion_rango' + contador + '"]').addClass('conteoinputs');
    contentToMove.find('input[id="rango_desde' + contador + '"]').addClass('conteoinputs');
    contentToMove.find('input[id="rango_hasta' + contador + '"]').addClass('conteoinputs');
    contentToMove.find('button[id="deleted_range"]').attr('onclick', 'Rowdeleted_range(' + contador + ')');
    contentToMove.attr('id', 'range-' + contador);
//console.log(contentToMove.find('button[id="deleted"]'));
    $('#pastecontent_range').append(contentToMove); // Agregar el contenido clonado al nuevo div
    $('#add-range').data('conteo-range', contador);

    validation.Crear();
    let cantidad_rangos_true = parseInt($('#totalrangos_true').val());

    cantidad_rangos_true < 1 ? $('#guardar').attr('disabled', true) : $('#guardar').removeAttr('disabled', false);

}
//PERMITE ELIMINAR UN RANGO
function Rowdeleted_range(value) {

    const rangeId = value;
    $(`#range-${rangeId}`).remove();
    var total = $("#totalrangos_true").val();
    var total_ran = total - 1;
    $("#totalrangos_true").val(total_ran);
    let cantidad_rangos_true = parseInt($('#totalrangos_true').val());
    //SI LA CANTIDAD DE RANGOS ES 0 DESACTIVAMOS EL BOTON DE GUARDAR, DE LO CONTRARIO LO ACTIVAMOS

    if (cantidad_rangos_true < 1) {
        $('#guardar_evaluacion').attr("disabled", "disabled");
    } else {
        $('#guardar_evaluacion').removeAttr("disabled");
    }
}

function Destroy(value) {
    event.preventDefault();
    Swal.fire({
        title: '!Atención!',
        text: 'Se procedera a eliminar la evaluacion de Habilidades. Esta accion no se puede revertir, ¿Esta de acuerdo?',
        icon: 'info',
        showCancelButton: true,
        showConfirmButton: true,
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Confirmar',
    }).then((result) => {     // Se agrega el método .then()
        if (result.isConfirmed) {

            $('#destroyeeval' + value).submit();
        }
    });
}