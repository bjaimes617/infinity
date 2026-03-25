$(function () {

    if ($('.mesdate').length != 0) {
        $('.mesdate').datetimepicker({
            format: 'YYYY',
            maxDate: moment(),
        });
    }

});

$(function () {
    if ($('.anioactual').length != 0) {
        $('.anioactual').datetimepicker({
            format: 'YYYY',
            minDate: moment(),
            maxDate: moment()
        });
    }

});


$('#create-arc').validate({
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
        form.submit();
    }
});