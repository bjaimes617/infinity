$(function () {
    if ($('#items_table').length != 0) {
        $("#items_table").DataTable({
            "lengthMenu": [10, 20, 15, 30, 50, 60],
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "ordering": true,
            //"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        });
    }
});

$("#cod").blur(function () {
    var url = $(this).data('href');
    var data = {
        cod: $(this).val(),
        _token: $("input[name=_token]").val()
    }
    $.ajax({
        type: 'POST',
        url: url,
        data: data,
        dataType: 'json',
        success: function (data)
        {
            if (data) {
                var Toastr = main();
                Toastr.fire({
                    icon: 'info',
                    title: "&nbsp; El Codigo Ingresado Ya existe.",
                });
                $('#save').attr('disabled', 'disabled');
            } else {
                $('#save').removeAttr('disabled', 'disabled');
            }
        }
    });
});

$('#create-items').validate({
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