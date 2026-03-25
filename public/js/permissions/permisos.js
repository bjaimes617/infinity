$(function () {
    if ($('#permisos_table').length != 0) {
        $("#permisos_table").DataTable({
            "lengthMenu": [15, 20,15,30,50,60],
            "responsive": true,
            "lengthChange":true,
            "autoWidth": false,
            "ordering": true,
            //"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        });
    }
}); 

$('#permission').validate({
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
        
        var subaccion = $("#subaccion").val() !== '' ? '.'+$("#subaccion").val() : '';        
        var valor = $('#modulo').val()+'.'+$("#accion").val()+subaccion;
        $("#slug").val(valor);
        
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