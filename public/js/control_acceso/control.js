$(function () {
    if ($('#datatable-visitantes').length != 0) {
        $("#datatable-visitantes").DataTable({
            "lengthMenu": [10, 20, 15, 30, 50, 60],
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "ordering": true,
            //"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        });
    }

    if ($('.dateter').length != 0) {
        $('.dateter').datetimepicker({
            format: 'DD/MM/YYYY'
        });
    }

    if ($('.daterange').length !== 0) {
        $('.daterange').daterangepicker({
            format: 'DD/MM/YYYY'
        });
    }

});

$(function () {
    if ($('.select2').length != 0) {
        $('.select2').select2({ theme: 'bootstrap4' });
    }
});

$('#area_trabajo').on('change', function () {
    var area = $(this).val();
    var url = $(this).data('href');
    var _token = $("input[name='_token']").val();
    $.ajax({
        type: 'POST',
        url: url,
        data: { area: area, _token: _token },
        dataType: 'json',
        success: function (data) {
            $('#carteras').empty();
            $('#carteras').append($("<option></option>").attr("value", '').text("[SELECCIONE]"));
            for (i = 0; i < data.length; i++) {
                $('#carteras').append($("<option></option>").attr("value", data[i].id).text(data[i].nombre_cartera));
            }
        }
    });
});

$('#carteras').on('change', function () {
    var cartera = $(this).val();
    var url = $(this).data('href');
    var _token = $("input[name='_token']").val();
    $.ajax({
        type: 'POST',
        url: url,
        data: { cartera: cartera, _token: _token },
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

$("#busquedalp").click(function () {

    var url = $("#url").val();
    var data = {
        area: $('#area_trabajo').val(),
        cartera: $('#carteras').val(),
        estatus: $('#estatus').val(),
        _token: $("input[name='_token']").val()
    };

    if (data.area == null || data.area == '') {
        $(function () {
            var Toastr = main();
            Toastr.fire({
                icon: 'error',
                title: "&nbsp; Debe Seleccionar una Gerencia/Unidad"
            });
        });
    } else {
        if ($.fn.dataTable.isDataTable('#datatable-dir_personal')) {
            $('#tablelistado').fadeOut();
            $('#datatable-dir_personal').DataTable().destroy();
        }
        $('#datatable-dir_personal').DataTable({
            "initComplete": function (settings, json) {
                $('#tablelistado').fadeIn();
            },
            responsive: true,
            "ordering": true,
            "autoWidth": false,
            "ajax": {
                "url": url,
                "dataType": "json",
                "type": "POST",
                "data": data,
                dataSrc: ""
            },
            "lengthMenu": [10, 20, 50, 75],
            "bPaginate": true,
            "bProcessing": true,
            "columns": [
                { mData: 'id' },
                { mData: 'cedula' },
                { mData: 'nombres' },
                { mData: 'area_trabajo' },
                { mData: 'cartera' },
                { mData: 'skill' },
                { mData: 'cargo' },
                { mData: 'sede' },
                { mData: 'estatus' },
            ]
        });
    }
});

$("#select_all").on('change', function () {
    $("input:checkbox").prop('checked', $(this).prop("checked"));
});

$('#qrgererate').click(function (e) {

    if ($('#select_all').prop('checked')) {
        var id = $("input[name='personalid[]']").map(function () {
            return $(this).val();
        }).get();
    } else {
        var id = $('[name="personalid[]"]:checked').map(function () {
            return this.value;
        }).get();
    }

    if (id.length == 0) {
        e.preventDefault();
        var Toastr = main();
        $(function () {
            Toastr.fire({
                icon: 'error',
                title: "&nbsp; Debe de seleccionar al menos una persona.",
            });
        });
    } else {
        $("#qr.generator.table").submit();
        setTimeout(function () {
            location.reload();
        }, 1000);
    }



});

$('#deletevisitantes').click(function (e) {

    if ($('#select_all').prop('checked')) {
        var id = $("input[name='listado[]']").map(function () {
            return $(this).val();
        }).get();
    } else {
        var id = $('[name="listado[]"]:checked').map(function () {
            return this.value;
        }).get();
    }
    if (id.length == 0) {
        e.preventDefault();
        var Toastr = main();
        $(function () {
            Toastr.fire({
                icon: 'error',
                title: "&nbsp; Debe de seleccionar al menos registro",
            });
        });
    } else {
        $("#delete-visitantes").submit();
        setTimeout(function () {
            location.reload();
        }, 1000);
    }
});

$("#create-report").validate({
    lang: 'es',
    errorElement: 'span',
    errorPlacement: function (error, element) {
        error.addClass('is-invalid text-sm');
        element.closest('.form-group').append(error);
    },
    highlight: function (element, errorClass, validClass) {
        $(element).addClass('is-invalid');
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass('is-invalid');
    },
    submitHandler: function (form) {
        //event.preventDefault();
        form.submit();
    }
});