$(function () {
    if ($('#roles_table').length != 0) {
        $("#roles_table").DataTable({
            "lengthMenu": [15, 20, 15, 30, 50, 60],
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "ordering": true,
            //"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        });
    }

    if ($('.select2').length != 0) {
        $(".select2").select2({            
            'placeholder': "[Seleccione una o mas Opciones]",
            'minimumInputLengthopcion': 0,
            'minimumResultsForSearch': 0,          
        });
        // con esto forzamos que el selector no se envie vacio al ser un selector multiple o individual
        $('.select2').rules('add', {
            selectMultipleRequired: true
        });
    }
});

$.validator.addMethod("selectMultipleRequired", function (value, element) {
    $(element).closest('.select2').addClass('is-invalid');
    return $(element).val() !== null && $(element).val().length > 0;
},"Debe seleccionar al menos una opción.");
    
    
$('#roles').validate({
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

function Destroy(id){  
    Swal.fire({
        title: '¿Esta seguro en eliminar este registro?, Tenga en cuenta que esto afectara la funcionalidad del sistema.',
        showDenyButton: false,
        showCancelButton: true,
        confirmButtonText: "Procesar",
        denyButtonText: "Regresar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            $('#eliminar' + id).submit();
        }
    });
}
