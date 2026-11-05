$(function () {
    bsCustomFileInput.init();
});

$.validator.addMethod(
    "excelOrAny",
    function (value, element) {
        // Obtener la extensión del archivo
        var extension = value.split(".").pop().toLowerCase();
        // Definir las extensiones permitidas para Excel
        var excelExtensions = ["xls", "xlsx"];
        // Comprobar si la extensión es de Excel o cualquier otra
        return excelExtensions.includes(extension);
    },
    "Por favor, sube un archivo de XLS o XlSX."
);

$("#update-dashboard-required").validate({
    ignore: [],
    lang: "es",
    errorElement: "span",
    validClass: "is-valid",
    rules: {
        archivo: {
            required: true,
            excelOrAny: true, // Usar el método de validación personalizado
        },
    },

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
    submitHandler: function (form, event) {
        event.preventDefault();

        /* $.blockUI({
            message:
                '<h4><img style="width: 100px; height: 100px;" src="' +
                asset +
                '" />\n\
                    <p>Espere unos instantes, mientras Procesamos...</p></h4>',
        });*/

        let formData = new FormData(form);
        formData.append("mes", $("#mes").val());

        $(".btn btn-primary").attr("disabled", "disabled");
        $.ajax({
            url: form.action,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $.unblockUI();
                $(".btn btn-primary").attr("disabled", "");
                if (response.success) {
                    toastr.success(response.message);
                    form.reset();
                } else {
                    toastr.error(response.message);
                }
            },
            fail: function (response) {
                $.unblockUI();
                $(".btn btn-primary").attr("disabled", "");
                toastr.error("Error al procesar la solicitud");
            },
        });
    },
});

$(document).ready(function () {
    $(document).on("input", ".input-number", function () {
        this.value = this.value.replace(/[^0-9]/g, "");
    });

    if ($("#datatable-skills").length != 0) {
        $("#datatable-skills").DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
            ordering: true,
            columnDefs: [{ orderable: false, targets: 0 }],
            dom:
                "<'row'<'col-12'f>>" +
                "<'row'<'col-12'tr>>" +
                "<'row mt-2'<'col-12 d-flex justify-content-center'p>>",
            language: {
                search: "",
                searchPlaceholder: "Buscar skill...",
                paginate: {
                    previous: "«",
                    next: "»",
                },
                emptyTable: "No hay datos disponibles",
                zeroRecords: "No se encontraron resultados",
            },
        });
    }

    if ($("#datatable-motivosEgresos").length != 0) {
        $("#datatable-motivosEgresos").DataTable({
            responsive: true,
            lengthChange: false,
            autoWidth: false,
            ordering: true,
            columnDefs: [{ orderable: true, targets: [0] }],
            order: [2, "DESC"],
            dom:
                "<'row'<'col-12'f>>" +
                "<'row'<'col-12'tr>>" +
                "<'row mt-2'<'col-12 d-flex justify-content-center'p>>",
            language: {
                search: "",
                searchPlaceholder: "Buscar motivo de egreso...",
                paginate: {
                    previous: "«",
                    next: "»",
                },
                emptyTable: "No hay datos disponibles",
                zeroRecords: "No se encontraron resultados",
            },
        });
    }

    $("#modal-motivos-egresos").on("shown.bs.modal", function () {
        if ($.fn.DataTable.isDataTable("#datatable-motivosEgresos")) {
            $("#datatable-motivosEgresos").DataTable().columns.adjust().responsive.recalc();
        }
    });

    $("#mes")
        .datetimepicker({
            format: "MM/YYYY",
            maxDate: moment(),
            viewMode: "months", // para que solo muestre meses y años
        })
        .on("dp.change", function (e) {
            var selectedMonth = $(this).val();
            if (e.date) {
                selectedMonth = e.date.format("MM/YYYY");
            }
            if (selectedMonth) {
                UpdateDisplayAll(selectedMonth, $(this).data("href"));
            }
        });

    $(".connectedSortable").sortable({
        placeholder: "sort-highlight",
        connectWith: ".connectedSortable",
        handle: ".card-header, .nav-tabs",
        forcePlaceholderSize: true,
        zIndex: 999999,
    });
    $(".connectedSortable .card-header").css("cursor", "move");

    // jQuery UI sortable for the todo list
    $(".todo-list").sortable({
        placeholder: "sort-highlight",
        handle: ".handle",
        forcePlaceholderSize: true,
        zIndex: 999999,
    });

    barchatjsPersonalActive();
    piechatMoptivosdeEgreso();
});

var personalChartInstance = null;
function ensureArray(val) {
    if (typeof val === "string") {
        try {
            return JSON.parse(val);
        } catch (e) {
            return [];
        }
    }
    return Array.isArray(val) ? val : [];
}

function barchatjsPersonalActive(customChartData) {
    var chartCanvasElement = $("#barChart");
    if (!chartCanvasElement.length) return;

    var barChartCanvas = chartCanvasElement.get(0).getContext("2d");
    var dataToUse = customChartData
        ? customChartData
        : typeof areaChartData !== "undefined"
        ? areaChartData
        : {};
    var barChartData = $.extend(true, {}, dataToUse);

    if (personalChartInstance) {
        personalChartInstance.destroy();
    }

    var barChartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        datasetFill: false,
        scales: {
            yAxes: [
                {
                    ticks: {
                        beginAtZero: true,
                    },
                    scaleLabel: {
                        display: true,
                        labelString: "Cantidad",
                    },
                },
                {
                    id: "y-cumplimiento",
                    position: "right",
                    ticks: {
                        suggestedMax: 100,
                        beginAtZero: true,
                        callback: function (value) {
                            return value + "%";
                        },
                    },
                    scaleLabel: {
                        display: true,
                        labelString: "% Cumplimiento",
                    },
                    gridLines: {
                        drawOnChartArea: false,
                    },
                },
            ],
        },
    };

    personalChartInstance = new Chart(barChartCanvas, {
        type: "bar",
        data: barChartData,
        options: barChartOptions,
    });
}

var pieChartInstance = null;
function piechatMoptivosdeEgreso(customChartData) {
    var pieCanvasElement = $("#pieChartAbsentismo");
    if (!pieCanvasElement.length) return;

    var pieChartCanvas = pieCanvasElement.get(0).getContext("2d");
    var dataToUse = customChartData
        ? customChartData
        : typeof chartEgresos !== "undefined"
        ? chartEgresos
        : { labels: [], datasets: [{ data: [] }] };

    if (pieChartInstance) {
        pieChartInstance.destroy();
    }

    var pieOptions = {
        legend: {
            display: true,
            position: "right",
        },
        maintainAspectRatio: false,
        responsive: true,
    };

    pieChartInstance = new Chart(pieChartCanvas, {
        type: "doughnut",
        data: dataToUse,
        options: pieOptions,
    });
}

function UpdateDisplayAll(mes, url) {
    if (!mes || !url) return;

    $.ajax({
        url: url,
        type: "POST",
        data: {
            mes: mes,
            _token: $('input[name="_token"]').val(),
        },
        success: function (response) {            

            // Actualizar tarjetas de totales (info-boxes)
            if (response.totalrequeridos !== undefined) {
                $("#total-requeridos").html(
                    response.totalrequeridos + " <small>Requeridos</small>"
                );
            }
            if (response.totalactivos !== undefined) {
                $("#total-activos").html(
                    response.totalactivos + " <small>Activos</small>"
                );
            }
            if (response.totaldiferencial !== undefined) {
                $("#total-diferencial").html(
                    response.totaldiferencial + " <small>Diferencia</small>"
                );
            }

            // Normalizar arrays
            var labels = ensureArray(response.chartLabels);
            var requeridos = ensureArray(response.chartRequeridos);
            var activos = ensureArray(response.chartActivos);
            var cumplimiento = ensureArray(response.chartcumplimiento);

            var updatedChartData = {
                labels: labels,
                datasets: [
                    {
                        label: "Requeridos",
                        backgroundColor: "#1B3155",
                        borderColor: "#1B3155",
                        order: 2,
                        data: requeridos,
                    },
                    {
                        label: "Activos",
                        backgroundColor: "#BE3A8D",
                        borderColor: "#BE3A8D",
                        order: 2,
                        data: activos,
                    },
                    {
                        label: "% Cumplimiento",
                        type: "line",
                        order: 1,
                        backgroundColor: "rgba(255, 193, 7, 0.2)",
                        borderColor: "#FFC107",
                        borderWidth: 2,
                        fill: false,
                        pointBackgroundColor: "#FFC107",
                        pointBorderColor: "#FFC107",
                        pointRadius: 4,
                        data: cumplimiento,
                        yAxisID: "y-cumplimiento",
                    },
                ],
            };
            ///enviamos los dataset a la funcion principal para actualizarlos
            barchatjsPersonalActive(updatedChartData);

            // Actualizar donut de egresos
            if (
                response.chartEgresosLabels !== undefined &&
                response.chartEgresosData !== undefined
            ) {
                var egresosLabels = ensureArray(response.chartEgresosLabels);
                var egresosData = ensureArray(response.chartEgresosData);
                var chartColors = [
                    "#BE3A8D",
                    "#00a65a",
                    "#8C8F93",
                    "#1B3155",
                    "#3c8dbc",
                    "#d2d6de",
                    "#BE3A8D",
                    "#6f42c1",
                    "#20c997",
                    "#e83e8c",
                ];

                var updatedEgresosData = {
                    labels: egresosLabels,
                    datasets: [
                        {
                            label: "Total Egresos",
                            backgroundColor: chartColors,
                            data: egresosData,
                        },
                    ],
                };
                piechatMoptivosdeEgreso(updatedEgresosData);
            }

            // recargamos a tabla con los nuevos datos
            if ($("#datatable-skills").length != 0) {
                $("#datatable-skills").DataTable({
                    responsive: true,
                    lengthChange: false,
                    autoWidth: false,
                    ordering: true,
                    destroy: true,
                    columnDefs: [{ orderable: false, targets: 0 }],
                    dom:
                        "<'row'<'col-12'f>>" +
                        "<'row'<'col-12'tr>>" +
                        "<'row mt-2'<'col-12 d-flex justify-content-center'p>>",
                    language: {
                        search: "",
                        searchPlaceholder: "Buscar skill...",
                        paginate: {
                            previous: "«",
                            next: "»",
                        },
                        emptyTable: "No hay datos disponibles",
                        zeroRecords: "No se encontraron resultados",
                    },
                    data: response.datos || [],
                    columns: [
                        {
                            data: "cartera",
                            render: function (data, type, row) {
                                return (
                                    '<div class="custom-control custom-checkbox">' +
                                    '<input class="custom-control-input" type="checkbox" id="customCheckbox' +
                                    data +
                                    '" value="' +
                                    data +
                                    '">' +
                                    '<label for="customCheckbox' +
                                    data +
                                    '" class="custom-control-label"></label>' +
                                    "</div>"
                                );
                            },
                        },
                        { data: "cartera" },
                        { data: "requerido" },
                        { data: "activos" },
                        { data: "diferencial" },
                    ],
                });
            }

            if ($("#datatable-motivosEgresos").length != 0) {
                var motivosData = [];
                if (response.datos && Array.isArray(response.datos)) {
                    response.datos.forEach(function (dato) {
                        var motivos = dato.motivos;
                        if (typeof motivos === "string") {
                            try {
                                motivos = JSON.parse(motivos);
                            } catch (e) {
                                motivos = null;
                            }
                        }
                        if (motivos && typeof motivos === "object") {
                            Object.keys(motivos).forEach(function (key) {
                                motivosData.push({
                                    cartera: dato.cartera,
                                    motivo: key,
                                    total: Number(motivos[key]) || motivos[key],
                                    clase_bg: dato.clase_bg || "",
                                });
                            });
                        }
                    });
                } else if (response.motivos && Array.isArray(response.motivos)) {
                    motivosData = response.motivos;
                }

                $("#datatable-motivosEgresos").DataTable({
                    responsive: true,
                    lengthChange: false,
                    autoWidth: false,
                    ordering: true,
                    destroy: true,
                    data: motivosData,
                    columnDefs: [{ orderable: true, targets: [0] }],
                    order: [2, "DESC"],
                    createdRow: function (row, data, dataIndex) {
                        if (data.clase_bg) {
                            $(row).addClass(data.clase_bg);
                        }
                    },
                    dom:
                        "<'row'<'col-12'f>>" +
                        "<'row'<'col-12'tr>>" +
                        "<'row mt-2'<'col-12 d-flex justify-content-center'p>>",
                    language: {
                        search: "",
                        searchPlaceholder: "Buscar motivo de egreso...",
                        paginate: {
                            previous: "«",
                            next: "»",
                        },
                        emptyTable: "No hay datos disponibles",
                        zeroRecords: "No se encontraron resultados",
                    },
                    columns: [
                        { data: "cartera" },
                        {
                            data: "motivo",
                            render: function (data) {
                                return "<b>" + (data || "") + "</b>";
                            },
                        },
                        { data: "total" },
                    ],
                });
            }
        },
        error: function (xhr, status, error) {

            if (xhr.status === 404) {
                toastr.info(xhr.responseJSON.message);
            } 
            
            console.error(
                "Error al actualizar datos:",
                error,
                xhr.responseText
            );
        },
    });
}
