if ($('.daterange').length !== 0) {
    $('.daterange').daterangepicker({
        locale: {
            format: 'DD/MM/YYYY'
        }
    });
}

$(function () {
    if ($('.select2').length != 0) {
        $('.select2').select2({theme: 'bootstrap4',
        'placeholder': "[TODOS]", });
    }
});

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
        success: function (data)
        {
            $('#carteras').empty();
            $('#carteras').append($("<option></option>").attr("value", '').text("[TODOS]"));
            $('#skill').empty();
            $('#skill').append($("<option></option>").attr("value", '').text("[TODOS]"));
            for (i = 0; i < data.length; i++)
            {
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
        success: function (data)
        {
            $('#skill').empty();
            $('#skill').append($("<option></option>").attr("value", '').text("[TODOS]"));
            for (i = 0; i < data.length; i++)
            {
                $('#skill').append($("<option></option>").attr("value", data[i].id).text(data[i].nombre_skill));
            }
        }
    });
});

$("#create-report").validate({
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
        form.submit();
    }
});