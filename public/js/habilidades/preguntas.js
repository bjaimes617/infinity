
$(document).ready(function () {
      if ($('.select2').length !== 0) {
        $('.select2').select2({
            placeholder: "Seleccione una o mas Opciones",
            theme: 'bootstrap4'
        });
    }
    
    validation.init();
});

var validation = function () {
    var Preguntas = function () {
        $('#preguntas_store').validate({
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
                Swal.fire({
                    title: '!Atención!',
                    text: 'Se procedera a actualizar las preguntas asociadas a la categoria. ¿Esta de acuerdo con los valores ingresados?',
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
        init: function () {
            Preguntas();
        }
    };
}();

///APARTADO DE PREGUNTAS
$('#evaluacion').on('change', function () {
    var evaluacion = $(this).val();
    var url = $(this).data('href');
    var _token = $("input[name=_token]").val();

    $.ajax({
        type: 'POST',
        url: url,
        data: {evaluacion: evaluacion, _token: _token},
        dataType: 'json',
        success: function (data) {
            if ($.fn.dataTable.isDataTable('#categorias_table')) {
                $('#tablelistado').fadeOut();
                $('#categorias_table').DataTable().destroy();
            }

            $('#categorias_table').DataTable({
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
                    {mData: 'cod'},
                    {mData: 'categoria'},
                    {mData: 'acciones'},                   
                ]
            });
        }
    });
});

function add_question() {

    let cantidad_preguntas = parseInt($('#cantidad_preguntas').val());
    let cantidad_preguntas_true = parseInt($('#cantidad_preguntas_true').val());
    var dis = $('#distribucion').val();
    var valor_base = $('.valor-base').val();
    var suma = 0;

    // Seleccionar todos los inputs cuyo id empieza con 'valor_pregunta'
    $('input[id^="valor_pregunta"]').each(function () {
        // Obtener el valor del campo actual, convertirlo a número y sumar
        let valor = parseFloat($(this).val()) || 0; // Si no es un número, usar 0
        suma += valor;
    });

    //SI LA DISTRIBUCION ES MANUAL NO REDONDEAMOS LA SUMA TOTAL
    if (dis == 2) {
        var total_sum = suma;
    } else {
        var total_sum = Math.round(suma);
    }
    $('#total_sum').val(total_sum);
    // Incrementar el contador
    cantidad_preguntas++;
    cantidad_preguntas_true++;

    // Actualizar el valor del input
    $('#cantidad_preguntas').val(cantidad_preguntas);
    $('#cantidad_preguntas_true').val(cantidad_preguntas_true);

    var contentToMove = $("#copycontent .row").clone();
    contentToMove.find('label[for="label1"]').text('Pregunta');
    contentToMove.find('label[for="label1"]').attr('for', 'nombre_pregunta' + cantidad_preguntas); // Actualizar el atributo 'for' de las etiquetas        
    contentToMove.find('input[id="label1"]').attr('id', 'nombre_pregunta' + cantidad_preguntas); // Actualizar el atributo 'id' de los inputs
    contentToMove.find('input[name="label1"]').attr('name', 'nombre_pregunta' + cantidad_preguntas); // Actualizar el atributo 'name' de los inputs 

    contentToMove.find('label[for="label2"]').text('Valor');
    contentToMove.find('label[for="label2"]').attr('for', 'valor_pregunta' + cantidad_preguntas);

    contentToMove.find('input[id="label2"]').attr('id', 'valor_pregunta' + cantidad_preguntas);
    contentToMove.find('input[name="label2"]').attr('name', 'valor_pregunta' + cantidad_preguntas);

    contentToMove.find('input[id="label3"]').attr('id', 'cod_pregunta' + cantidad_preguntas);
    contentToMove.find('input[name="label3"]').attr('name', 'cod_pregunta' + cantidad_preguntas);

    contentToMove.find('button[id="deleted"]').attr('onclick', 'Rowdeleted(' + cantidad_preguntas + ')');
    contentToMove.attr('id', 'pregunta-' + cantidad_preguntas);
//console.log(contentToMove.find('button[id="deleted"]'));
    $('#pastecontent').append(contentToMove); // Agregar el contenido clonado al nuevo div

    //SI LA DISTRIBUCION ES AUTOMATICA AL AGREGAR UNA PREGUNTA SE COLOCAN COMO SOLO LECTURA Y RECALCULA

    if (dis == 1) {
        $('#valor_pregunta' + cantidad_preguntas).attr('disabled', 'disabled');
        $('.valor-pregunta').attr('disabled', 'disabled');
        calcularResultados(valor_base);
    } else {
        $('#valor_pregunta' + cantidad_preguntas).val("");
    }
    //SI LA CANTIDAD DE PREGUNTAS ES 0 DESACTIVAMOS EL BOTON DE GUARDAR, DE LO CONTRARIO LO ACTIVAMOS
    validation.init();

    if (cantidad_preguntas_true < 1) {
        $('#guardar_preguntas').attr("disabled", "disabled");
    } else {
        $('#guardar_preguntas').removeAttr("disabled");
    }
}

//PERMITE ELIMINAR UNA PREGUNTA DE LA CATEGORIA

// Inicializa un array vacío
var valores = [];
var ids = [];
function Rowdeleted(value) {

    var suma = 0;
    const preguntaId = value;
    const codPregunta = $(`#pregunta-${preguntaId} #cod_pregunta${preguntaId}`).val();
    const IdPregunta = $(`#pregunta-${preguntaId} #id_pregunta${preguntaId}`).val();
    var dis = $('#distribucion').val();

    //SI LA DISTRIBUCION ES AUTOMICA CALCULAMOS LA SUMATORIA ANTES DE ELIMINAR LA PREGUNTA

    if (dis == 1) {

        // Seleccionar todos los inputs cuyo id empieza con 'valor_pregunta'
        $('input[id^="valor_pregunta"]').each(function () {
            // Obtener el valor del campo actual, convertirlo a número y sumar
            let valor = parseFloat($(this).val()) || 0; // Si no es un número, usar 0
            suma += valor;
        });

        var total_sum = Math.round(suma);

        $('#total_sum').val(total_sum);

    }

    if (codPregunta) {

        // Agregar múltiples valores
        valores = valores.concat(codPregunta);
        $('#codigos').val(valores);

    }

    if (IdPregunta) {

        // Agregar múltiples valores
        ids = ids.concat(IdPregunta);
        $('#id_ques').val(ids);

    }

    console.log(valores);

    $(`#pregunta-${preguntaId}`).remove();
    var valor_base = $('.valor-base').val();

    var total = $("#cantidad_preguntas_true").val();

    var total_questions = total - 1;

    $("#cantidad_preguntas_true").val(total_questions);

    let cantidad_preguntas_true = parseInt($('#cantidad_preguntas_true').val());

    //SI LA CANTIDAD DE PREGUNTAS ES 0 DESACTIVAMOS EL BOTON DE GUARDAR, DE LO CONTRARIO LO ACTIVAMOS

    if (cantidad_preguntas_true < 1) {

        $('#guardar_preguntas').attr("disabled", "disabled");

    } else {

        $('#guardar_preguntas').removeAttr("disabled");
    }

    //SI LA DISTRIBUCION ES AUTOMATICA RECALCULAMOS

    if (dis == 1) {

        calcularResultados(valor_base);

    }

    //SI LA DISTRIBUCION ES MANUAL CALCULAMOS LA SUMATORIA NUEVAMENTE CON LOS CAMPOS QUE QUEDARON VISIBLES

    if (dis == 2) {

        // Seleccionar todos los inputs cuyo id empieza con 'valor_pregunta'
        $('input[id^="valor_pregunta"]').each(function () {
            // Obtener el valor del campo actual, convertirlo a número y sumar
            let valor = parseFloat($(this).val()) || 0; // Si no es un número, usar 0
            suma += valor;
        });

        var total_sum = suma;

        $('#total_sum').val(parseFloat(suma.toFixed(3)));

    }

}

// Función para calcular los resultados
$(document).on('input', '.valor-base', function () {
    var valor_base = $(this).val();
    var dis = $('#distribucion').val();
    $('.valor-base-h').val(valor_base);
    //SI LA DISTRIBUACION ES AUTOMATICA CALCULAMOS LOS RESULTADOS
    if (dis == 1) {
        calcularResultados(valor_base);
    }
});

function calcularResultados(value) {
    const valorBase = parseFloat(value) || 0;
    const cantidadCamposAdicionales = $('.valor-pregunta').length - 1;

    console.log(valorBase, cantidadCamposAdicionales);

    const resultadoDividido = cantidadCamposAdicionales > 0 ? valorBase / cantidadCamposAdicionales : 0;

    $('.valor-pregunta').val(resultadoDividido.toFixed(3));

}

//FUNCION QUE PERMITE COLOCAR LOS VALORES DE LAS PREGUNTAS AUTOMATICO O MANUAL

function distribuir() {

    var dis = $('#distribucion').val();
    var valor_base = $('.valor-base').val();
    var suma = 0;
    //SI LA DISTRIBUCION ES AUTOMATICA LOS CAMPOS DE LAS PREGUNTAS PASAN A SOLO LECTURA Y SE HACE EL CALCULO, EL CAMPO OCULTO TOTAL SUM QUEDA INUTILIZABLE
    if (dis == 1) {
        $('.valor-pregunta').attr('readonly', 'readonly');
        $('#total_sum').val(0);
        calcularResultados(valor_base);
    } else {
        //DE LO CONTRARIO SI ES MANUAL SE QUITA EL ATRIBUTO DE SOLO LECTURA Y REALIZAMOS CALCULOS PARA TOTAL SUM
        // Seleccionar todos los inputs cuyo id empieza con 'valor_pregunta'
        $('input[id^="valor_pregunta"]').each(function () {
            // Obtener el valor del campo actual, convertirlo a número y sumar
            let valor = parseFloat($(this).val()) || 0; // Si no es un número, usar 0
            suma += valor;
        });
        var total_sum = suma;
        $('#total_sum').val(total_sum);
        $('.valor-pregunta').removeAttr('disabled');
    }

}

//FUNCION QUE RECALCULA LA SUMATORIA AL ESCRIBIR EN LOS CAMPOS DE LOS VALORES DE LAS PREGUNTAS

$('.valor-pregunta').on('input', function () {
    var suma = 0;
    // Seleccionar todos los inputs cuyo id empieza con 'valor_pregunta'
    $('input[id^="valor_pregunta"]').each(function () {
        // Obtener el valor del campo actual, convertirlo a número y sumar
        let valor = parseFloat($(this).val()) || 0; // Si no es un número, usar 0
        suma += valor;
    });
    var total_sum = suma;
    $('#total_sum').val(parseFloat(suma.toFixed(3)));

});

function sumar() {
    var suma = 0;
    // Seleccionar todos los inputs cuyo id empieza con 'valor_pregunta'
    $('input[id^="valor_pregunta"]').each(function () {
        // Obtener el valor del campo actual, convertirlo a número y sumar
        let valor = parseFloat($(this).val()) || 0; // Si no es un número, usar 0
        suma += valor;
    });

    var total_sum = suma;
    $('#total_sum').val(parseFloat(suma.toFixed(3)));
}