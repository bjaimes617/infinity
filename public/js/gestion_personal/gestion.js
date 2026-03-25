// funcion INDEX PERSONAL
$('#area_trabajo').on('change', function () {

    var data = {
        _token: $('input[name="_token"]').val(),
        area: $(this).val()
    };
    var url = $(this).data('href');

    $.ajax({
        type: 'POST',
        url: url,
        data: data,
        dataType: 'json',
        success: function (data) {
            $('#carteras').empty();
            $('#carteras').append($("<option></option>").attr("value", '').text("[TODOS]"));
            $('#skill').empty();
            $('#skill').append($("<option></option>").attr("value", '').text("[TODOS]"));
            for (i = 0; i < data.length; i++) {
                $('#carteras').append($("<option></option>").attr("value", data[i].id).text(data[i].nombre_cartera));
            }
        },
        error: function (data) {
            var Toastr = main();
            Toastr.fire({
                icon: 'error',
                title: data.responseJSON.message,
            });
        }
    });
});
// funcion INDEX PERSONAL
$('#carteras').on('change', function () {

    var data = {
        _token: $('input[name="_token"]').val(),
        cartera: $(this).val()
    };
    var url = $(this).data('href');
    $.ajax({
        type: 'POST',
        url: url,
        data: data,
        dataType: 'json',
        success: function (data) {
            $('#skill').empty();
            $('#skill').append($("<option></option>").attr("value", '').text("[TODOS]"));
            for (i = 0; i < data.length; i++) {
                $('#skill').append($("<option></option>").attr("value", data[i].id).text(data[i].nombre_skill));
            }
        }
    });
});
// funcion INDEX PERSONAL

$("#busquedalp").click(function () {

    var url = $("#url").val();
    var data = {
        area: $('#area_trabajo').val(),
        cartera: $('#carteras').val(),
        skill: $('#skill').val(),
        personal: $('#personals').val(),
        estatus: $('#estatus').val(),
        _token: $('input[name="_token"]').val(),
    };

    if ($.fn.dataTable.isDataTable('#datatable-dir_personal')) {
        $('#tablelistado').fadeOut();
        $('#datatable-dir_personal').DataTable().destroy();
    }
    $('#datatable-dir_personal').DataTable({
        "initComplete": function (settings, json) {
            $('#tablelistado').fadeIn();
        },
        "responsive": true,
        "ordering": true,
        order: [[1, "asc"]],
        "autoWidth": false,
        "ajax": {
            "url": url,
            "dataType": "json",
            "type": "POST",
            "data": data,
            dataSrc: "",
            "error": function (xhr, error, thrown) {
                // Aquí puedes manejar el error
                console.log(xhr, error, thrown);
                var Toastr = main();
                Toastr.fire({
                    icon: 'error',
                    title: xhr.responseJSON.message
                })
            }
        },
        "lengthMenu": [10, 50, 100, 500],
        "bPaginate": true,
        "bProcessing": true,
        "columns": [
            { mData: 'cedula' },
            { mData: 'nombres' },
            { mData: 'dateingreso' },
            { mData: 'area_trabajo' },
            { mData: 'cartera' },
            { mData: 'skill' },
            { mData: 'cargo' },
            { mData: 'contrato' },
            { mData: 'estatus' },
            { mData: 'acciones' }
        ]
    });
});

//////UBICAR
if ($('#datatable-areas').length != 0) {
    $("#datatable-areas").DataTable({
        responsive: true,
        "lengthChange": true,
        "autoWidth": false,
        "lengthMenu": [10, 20, 50, 75, 100, 200],
        "buttons": ["copy", "csv", "excel", "print"]
    }).buttons().container().appendTo('#datatable-areas_wrapper .col-md-6:eq(0)');
}

$('.delete').click(function (event) {
    event.preventDefault();
    $('#confirm').modal('show');
    var route = $(this).data("url");
    var _token = $("input[name='_token']").val();
    var parent = $(this).parent();
    $('#delete').click(function (event) {
        event.preventDefault();
        $.ajax({
            url: route,
            headers: { 'X-CSRF-TOKEN': _token },
            type: 'DELETE',
            dataType: 'json',
            success: function (msg) {

                $('#confirm').modal('hide');
                setTimeout(window.location.href = $('#urlReturn').val(), 2000);
            }
        });
    });
});

$(function () {
    if ($('#personal_deleted').length != 0) {
        $("#personal_deleted").DataTable({
            "lengthMenu": [10, 20, 15, 30, 50, 60],
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "ordering": true,
            //"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        });
    }
});

$(function () {
    if ($('.select2').length != 0) {
        $('.select2').select2({ theme: 'bootstrap4' });
    }
});

$(function () {
    if ($('#historico-table').length != 0) {
        $("#historico-table").DataTable({
            "lengthMenu": [10, 20, 15, 30, 50, 60],
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "ordering": false,
            //"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        });
    }
});

if ($('.duallistbox').length != 0) {
    $(function () {
        $('.duallistbox').bootstrapDualListbox({
            nonSelectedListLabel: 'Non-selected',
            selectedListLabel: 'Selected',
            preserveSelectionOnMove: 'moved',
            moveOnSelect: false,
        });
    });
}

$('.input-number').on('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '');
});

if ($('.mesdate').length != 0) {
    $('.mesdate').datetimepicker({
        format: 'MM/YYYY',
        maxDate: moment(),
    });
}



$("#ShowFileReplicas").validate({
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

        event.preventDefault();

        $.ajax({
            type: form.method,
            url: form.action,
            data: $(form).serialize(),
            dataType: 'json',
            success: function (data) {

                if ($.fn.dataTable.isDataTable('#datatable-replicas_expedientes')) {
                    $('#tablelistado').fadeOut();
                    $('#datatable-replicas_expedientes').DataTable().destroy();
                }

                $('#datatable-replicas_expedientes').DataTable({
                    "initComplete": function (settings, json) {
                        $('#tablelistado').fadeIn();
                    },
                    responsive: true,
                    "ordering": false,
                    "autoWidth": false,
                    data: data,
                    "lengthMenu": [10, 20, 50, 75],
                    "bPaginate": true,
                    "bProcessing": true,
                    "columns": [
                        { mData: 'dia' },
                        { mData: 'nombres' },
                        { mData: 'acciones' }
                    ]
                });
            }, error: function (data) {
                $("#tablelistado").empty();
                var Toastr = main();
                Toastr.fire({
                    icon: 'error',
                    title: data.responseJSON.message,
                });
            }
        });
    }
});