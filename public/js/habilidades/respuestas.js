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
    var Respuestas = function () {
        $('#respuestas_store').validate({
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
                    text: 'Se procedera a actualizar las Respuestas asociadas a la Pregunta. ¿Esta de acuerdo con los valores ingresados?',
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
            Respuestas();
        }
    };
}();

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
            $('#categorias').append($("<option></option>").attr("value", "").text("[SELECCIONE CATEGORIA]"));
            for (i = 0; i < data.length; i++)
            {
                $('#categorias').append($("<option></option>").attr("value", data[i].id).text(data[i].cod_categoria + ' - ' + data[i].nombre_categoria));
            }
        }
    });
});

$('#categorias').on('change', function () {
    var evaluacion = $('#evaluacion').val();
    var categoria = $(this).val();
    var url = $(this).data('href');
    var _token = $("input[name=_token]").val();

    $('#preguntas').empty().trigger('change');

    $.ajax({
        type: 'POST',
        url: url,
        data: {evaluacion: evaluacion, categoria: categoria, _token: _token},
        dataType: 'json',
        success: function (data) {
            if ($.fn.dataTable.isDataTable('#preguntas_table')) {
                $('#tablelistado').fadeOut();
                $('#preguntas_table').DataTable().destroy();
            }

            $('#preguntas_table').DataTable({
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
                    {mData: 'pregunta'},
                    {mData: 'acciones'},
                ]
            });
        }
    });
});

//PERMITE AGREGAR RESPUESTAS

function add_answer() {

    let cantidad_respuestas = parseInt($('#cantidad_respuestas').val());
    let cantidad_respuestas_true = parseInt($('#cantidad_respuestas_true').val());
    var valor_base = $('.valor-base').val();

    // Incrementar el contador
    cantidad_respuestas++;
    cantidad_respuestas_true++;

    // Actualizar el valor del input
    $('#cantidad_respuestas').val(cantidad_respuestas);
    $('#cantidad_respuestas_true').val(cantidad_respuestas_true);

    var contentToMove = $("#copycontent .row").clone();
    contentToMove.find('label[for="label1"]').text('Respuesta');
    contentToMove.find('label[for="label1"]').attr('for', 'nombre_respuesta' + cantidad_respuestas); // Actualizar el atributo 'for' de las etiquetas        
    contentToMove.find('input[id="label1"]').attr('id', 'nombre_respuesta' + cantidad_respuestas); // Actualizar el atributo 'id' de los inputs
    contentToMove.find('input[name="label1"]').attr('name', 'nombre_respuesta' + cantidad_respuestas); // Actualizar el atributo 'name' de los inputs 

    contentToMove.find('label[for="label2"]').text('Valor');
    contentToMove.find('label[for="label2"]').attr('for', 'valor_respuesta' + cantidad_respuestas);

    contentToMove.find('input[id="label2"]').attr('id', 'valor_respuesta' + cantidad_respuestas);
    contentToMove.find('input[name="label2"]').attr('name', 'valor_respuesta' + cantidad_respuestas);

    contentToMove.find('input[id="label3"]').attr('id', 'cod_respuesta' + cantidad_respuestas);
    contentToMove.find('input[name="label3"]').attr('name', 'cod_respuesta' + cantidad_respuestas);

    contentToMove.find('button[id="deleted"]').attr('onclick', 'Rowdeleted(' + cantidad_respuestas + ')');
    contentToMove.attr('id', 'respuesta-' + cantidad_respuestas);
//console.log(contentToMove.find('button[id="deleted"]'));
    $('#pastecontent').append(contentToMove); // Agregar el contenido clonado al nuevo div
    $('#valor_respuesta' + cantidad_respuestas).val("");

//SI LA CANTIDAD DE RESPUESTAS ES 0 DESACTIVAMOS EL BOTON DE GUARDAR, DE LO CONTRARIO LO ACTIVAMOS
    validation.init();
    if (cantidad_respuestas_true < 1) {
        $('#guardar_respuestas').attr("disabled", "disabled");
    } else {
        $('#guardar_respuestas').removeAttr("disabled");
    }
}

// Inicializa un array vacío
var valores = [];

//PERMITE ELIMINAR UNA RESPUESTA

function Rowdeleted(value) {
    const respuestaId = value;
    const codRespuesta = $(`#respuesta-${respuestaId} #cod_respuesta${respuestaId}`).val();
    //ALMACENAR CODIGOS DE RESPUESTA EN CASO DE ELIMINACION

    if (codRespuesta) {
        // Agregar múltiples valores
        valores = valores.concat(codRespuesta);
        $('#codigos').val(valores);
    }

    console.log(valores);
    $(`#respuesta-${respuestaId}`).remove();
    var total = $("#cantidad_respuestas_true").val();
    var total_answers = total - 1;
    $("#cantidad_respuestas_true").val(total_answers);
    let cantidad_respuestas_true = parseInt($('#cantidad_respuestas_true').val());

    //SI LA CANTIDAD DE RESPUESTAS ES 0 DESACTIVAMOS EL BOTON DE GUARDAR, DE LO CONTRARIO LO ACTIVAMOS

    if (cantidad_respuestas_true < 1) {
        $('#guardar_respuestas').attr("disabled", "disabled");
    } else {
        $('#guardar_respuestas').removeAttr("disabled");
    }

}
