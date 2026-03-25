$(document).ready(function () {

    if ($('.select2').length !== 0) {
        $('.select2').select2({
            placeholder: "Seleccione una o mas Opciones",
            theme: 'bootstrap4'
        });
    }

    if ($('.daterange').length !== 0) {

        $('.daterange').daterangepicker({
            // Formato específico para mostrar
            locale: {
                format: 'DD/MM/YYYY' // Cambia esto al formato que necesites
            },
        });

    }

    var nuevaOpcion = new Option("[TODAS]", "TODAS");
    $('#gerencia').prepend(nuevaOpcion);
});

$('#gerencia').on('input', function () {

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

    $('#campanas').empty();

    var nuevaOpcion = new Option("[TODAS]", "TODAS");
    $('#campanas').prepend(nuevaOpcion);
    $.ajax({
        type: 'POST',
        url: url,
        data: data,
        dataType: 'json',
        success: function (data) {
            for (i = 0; i < data.length; i++)
            {
                $('#campanas').append($("<option></option>").attr("value", data[i].id).text(data[i].nombre_cartera));
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

$('#campanas').on('input', function () {

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

    var url = $(this).data('href');
    var data = {cartera: $(this).val(), autorize: true, _token: $("input[name=_token]").val(), };

    $.ajax({
        type: 'POST',
        url: url,
        data: data,
        dataType: 'json',
        success: function (data)
        {
            for (i = 0; i < data.length; i++)
            {
                $('#skill').append($("<option></option>").attr("value", data[i].id).text(data[i].nombre_skill));
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

$('#skill').on('input', function () {
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
    var skill = $(this).val();
    var evaluacion = $("#evaluacion").val();
    var url = $(this).data('href');
    var _token = $("input[name='_token']").val();
    $.ajax({
        type: 'POST',
        url: url,
        data: {evaluacion: evaluacion, skill: skill, _token: _token},
        dataType: 'json',
        success: function (data)
        {
            $('#personal_cargo').empty();
            // Llenar selector de cargos
            $('#personal_cargo').append($("<option></option>").attr("value", '').text("[SELECCIONE]"));
            for (i = 0; i < data.length; i++)
            {
                $('#personal_cargo').append($("<option></option>").attr("value", data[i].id_personal).text(data[i].primer_nombre + ' ' + data[i].primer_apellido));
            }

        }
    });
});

//FUNCION PARA TRAER EL HISTORICO DE CARGOS DE LA PERSONA
$('#personal_cargo').on('input', function () {

    var personal_id = $(this).val();
    var skill = $('#skill').val();
    var evaluacion = $('#evaluacion').val();
    var url = $(this).data('href');
    var _token = $("input[name='_token']").val();

    $.ajax({
        type: 'POST',
        url: url,
        data: {evaluacion: evaluacion, skill: skill, personal_id: personal_id, _token: _token},
        dataType: 'json',
        success: function (data)
        {
            $('#position_name').empty();
            $('#sede').val('');
            $('#rango').val('');

            // Llenar selector de cargos
            $('#position_name').append($("<option></option>").attr("value", '').text("[SELECCIONE]"));
            $.each(data.historico, function (index, cargo) {
                $('#position_name').append($('<option>', {
                    value: cargo.nombre_cargo,
                    text: cargo.nombre_cargo
                }));
            });

            // Llenar input de sede laboral
            $('#sede').val(data.sede);
            $('#rango').val(data.rango_fecha);
            $('#rango_eval').val(data.rango_fecha);
        }
    });

});
//FUNCION PARA GENERAR EL GRAFICO 

$('#generarGrafico').click(function () {

    var url = $(this).data('href');
    var _token = $("input[name='_token']").val();
    var skill = $('#skill').val();
    var evaluacion = $('#evaluacion').val();
    var area_trabajo = $('#gerencia').val();
    var carteras = $('#campanas').val();
    var personal_id = $('#personal_cargo').val();
    var position_name = $('#position_name').val();
    var rango_eval = $('#rango_eval').val();

    destruirGrafico(); // Destruir el gráfico existente

    if (skill == "" || evaluacion == "" || area_trabajo == "" || carteras == "" || personal_id == "" || position_name == "" || rango_eval == "") {

        var Toastr = main();
        Toastr.fire({
            icon: 'error',
            title: 'Por favor llene los campos vacios',
        });

    } else {
        $.ajax({
            url: url, // Ruta PHP
            type: 'POST',
            data: {
                _token: _token, // Token CSRF
                skill: skill,
                evaluacion: evaluacion,
                area_trabajo: area_trabajo,
                carteras: carteras,
                personal_id: personal_id,
                position_name: position_name,
                rango_eval: rango_eval,
            },
            success: function (data) {

                if (data.titulo == "") {
                    var Toastr = main();
                    Toastr.fire({
                        icon: 'error',
                        title: 'No hay datos con los parametros especificados para generar el grafico',
                    });
                } else {

                    var colores = data.datosGrafico.map(item => item.color);
                    var parametros = data.datosGrafico.map(item => item.nombre_parametro);
                    generarGrafico(data.titulo, data.labels, data.datos, colores, parametros);

                }
            }
        });
    }
});

let miGrafico = null; // Variable para almacenar la instancia del gráfico

const colorfondo = {
    id: 'customCanvasBackgroundColor',
    beforeDraw: (chart, args, options) => {
        const {ctx} = chart;
        ctx.save();
        ctx.globalCompositeOperation = 'destination-over';
        ctx.fillStyle = options.color || '#99ffff';
        ctx.fillRect(0, 0, chart.width, chart.height);
        ctx.restore();
    }
};

function generarGrafico(titulo, labels, datos, colores, parametros) {

    Chart.register(ChartDataLabels);

    $('#habilidad_grafico').show(); // Mostrar el canvas
    const ctx = document.getElementById('habilidad_grafico').getContext('2d');
    miGrafico = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                    label: '',
                    data: datos,
                    backgroundColor: colores,
                    borderColor: colores,
                    borderWidth: 1,
                    barThickness: 50, // Ajusta el ancho de las barras
                    barPercentage: 0.5 // Ajusta el ancho de las barras (0.0 - 1.0)
                }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: titulo,
                    padding: {
                        top: 10,
                        bottom: 30 // Aumenta el padding inferior para dar espacio al título
                    },
                    font: {
                        size: 16
                    }
                },
                datalabels: {
                    anchor: 'end', // Posición de la etiqueta: 'start', 'center', 'end'
                    align: 'top', // Alineación de la etiqueta: 'top', 'middle', 'bottom'
                    formatter: function (value, context) {
                        return parametros[context.dataIndex] + "\n" + value.toFixed(3); // Formatear a tres decimales
                    },
                    font: {
                        weight: 'bold' // Estilo de la fuente (opcional)
                    }
                },
                customCanvasBackgroundColor: {
                    color: 'white',
                }
            },
            scales: {
                x: {
                    ticks: {
                        font: {
                            size: 13, // Ajusta el tamaño de la fuente
                            weight: 'bold'
                        }
                    }
                },
                y: {
                    ticks: {
                        font: {
                            size: 13, // Ajusta el tamaño de la fuente
                            weight: 'bold'
                        }
                    },
                    beginAtZero: true,
                }
            },
        },
        plugins: [colorfondo],
    });

    // Obtener los valores de texto de los selectores
    var nombre = $('#personal_cargo option:selected').text();
    var cargo = $('#position_name option:selected').text();
    var sede = $('#sede').val();
    var rango = $('#rango').val();

    // Mostrar los valores en el modal
    $('#nombre').text(nombre);
    $('#cargo').text(cargo);
    $('#fecha').text(rango);
    $('#sede_m').text(sede);


    // Mostrar el modal
    $('#modal_grafico').modal('show');
}

function destruirGrafico() {
    if (miGrafico) {
        miGrafico.destroy();
        miGrafico = null; // Limpiar la variable
    }
}

$('#descargarGrafico').click(function () {

    var nombre = $('#personal_cargo option:selected').text();
    var cargo = $('#position_name option:selected').text();

    html2canvas(document.getElementById('grafico-content')).then(function (canvas) {
        // Convertir el canvas a una URL de datos JPG
        var imagen = canvas.toDataURL('image/jpeg');

        // Crear un enlace de descarga
        var link = document.createElement('a');
        link.download = nombre + ' ' + cargo + '.jpg';
        link.href = imagen;

        // Simular un clic en el enlace para descargar la imagen
        link.click();
    });
});

$('#reportes_create').validate({
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
        form.submit()
    }
});