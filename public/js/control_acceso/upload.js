$(function () {
  bsCustomFileInput.init();   
});

$.validator.addMethod("excelOrAny", function (value, element) {
    // Obtener la extensión del archivo
    var extension = value.split('.').pop().toLowerCase();
    // Definir las extensiones permitidas para Excel
    var excelExtensions = ["xls", "xlsx"];
    // Comprobar si la extensión es de Excel o cualquier otra
    return excelExtensions.includes(extension);
}, "Por favor, sube un archivo de XLS o XlSX.");

$('#qrgeneratortable').validate({
    ignore: [],
    lang: 'es',
    errorElement: 'span',
    validClass: "is-valid",
    rules: {
        archivo: {
            required: true,
            excelOrAny: true // Usar el método de validación personalizado
        }
    },

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
        $.blockUI({message: '<h4><img style="width: 100px; height: 100px;" src="' + asset + '" />\n\
                    <p>Espere unos instantes, mientras Procesamos...</p></h4>'});
        
        $('.btn btn-primary').attr('disabled', 'disabled');
        form.submit();
    }
});