$(function () {    
    if ($('#usuarios_table').length !== 0) {
        $("#usuarios_table").DataTable({
            "lengthMenu": [15, 20,15,30,50,60],
            "responsive": true,
            "lengthChange":true,
            "autoWidth": false,
            "ordering": true,
            //"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        });
    }
});

$(function () {
    if ($('#historial_table').length !== 0) {
        $("#historial_table").DataTable({
            "lengthMenu": [10, 20,15,30,50,60],
            "responsive": true,
            "lengthChange":true,
            "autoWidth": false,
            "ordering": true,
            "order": [[ 0, "desc" ]],
            //"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        });
    }
});

///habilita el select2 en los formularios de usuarios
if ($('.select2').length !== 0) {
        $(".select2").select2({            
            'placeholder': "[Seleccione una o más opciones]",
            'minimumInputLengthopción': 0,
            'minimumResultsForSearch': 0,          
        });       
    }

//check reset password en el edir de usuarios
$('#roles').change(function () {
console.log("ejecuta");
    var data = {
        id: $(this).val(),
        _token: $('input[name="_token"]').val()
    };
    $.ajax({
        type: 'POST',
        url: $(this).data('href'),
        data: data,
        dataType: 'json',
        success: function (data)
        {
            $('#permisos').removeAttr('disabled');
            
            $('#permisos option').prop('disabled', false);
            
            $('#permisos').val([]).trigger('change');
            
            const permisosOcultos = Array.isArray(data) ? data : [data];
            
            $('#permisos option').each(function () {             
                const optionValue = parseInt($(this).val());   
                if (data.includes(parseInt(optionValue))) {                     
                    $(this).prop('disabled', true); // Ocultar la opción
                }               
            });           
           
            $('#permisos').select2({
                placeholder: "Selecciona Permisos Adicionales",   
                allowClear: true           
            });             
        },
        error: function (data) {
            console.error("Error al obtener permisos:", data);
        }
    });

});

///validar formularios
$('#users').validate({
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

///destruir usuarios
function Destroy(id){  
    Swal.fire({
        title: '¿Esta seguro en eliminar este usuario?, Tenga en cuenta que esto afectara la funcionalidad del sistema.',
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

//tabla de todos los permisos
function ModalPermissions(id){
        
    var url = $('#searchPermissionsUsers').val();
    var data={
        user: id,
        _token: $('input[name="_token"]').val()
    };
    
       $.ajax({
        url: url,
        method: "POST",
        data: data,
        dataType: "JSON",
        success: function (data) {
        $('#modal-permisos').modal('show');   
        $('#RolPrincipal').html(data[1].name);
        
        if ($.fn.dataTable.isDataTable('#PermissionsUsers')) {
            $('#PermissionsUsers').DataTable().destroy();
        }    
            $('#PermissionsUsers').DataTable({
                "initComplete": function (settings, json) {

                },
                "responsive": true,
                "ordering": true,
                "autoWidth": false,
                "data": data[0],
                "lengthMenu": [15, 20, 50, 75],
                "bPaginate": true,
                "bProcessing": true,
                "columns": [
                    {mData: 'permiso'},
                    {mData: 'slug'},                  
                ]
            });
        },
        error: function (data) {
            $('#modal-permisos').hide();
            var Toastr = main();
            Toastr.fire({
                icon: 'error',
                title: data.responseJSON.message,
            });
        }
    });
    
}

function ModelTokens(id) {

    var url = $('#searchTokensUsers').val();
    var data = {
        user: id,
        _token: $('input[name="_token"]').val()
    };

    $.ajax({
        url: url,
        method: "POST",
        data: data,
        dataType: "JSON",
        success: function (data) {
            $('#modal-tokens-list').modal('show');

            if ($.fn.dataTable.isDataTable('#TokenListUser')) {
                $('#TokenListUser').DataTable().destroy();
            }
            $('#TokenListUser').DataTable({
                "initComplete": function (settings, json) {

                },
                "responsive": true,
                "ordering": true,
                "autoWidth": false,
                "data": data,
                "lengthMenu": [15, 20, 50, 75],
                "bPaginate": true,
                "bProcessing": true,
                "columns": [
                    {mData: 'name'},
                    {mData: 'encrypt'},
                    {mData: 'acciones'},
                ],
                createdRow: function (row, data, dataIndex) {
                    $(row).attr('data-id', data.id); // suponiendo que 'id' es el campo único
                }
            });
        },
        error: function (data) {
            $('#modal-tokens-list').hide();
            var Toastr = main();
            Toastr.fire({
                icon: 'error',
                title: data.responseJSON.message,
            });
        }
    });
}

function DeleteTokens(id) {
  
    Swal.fire({
        title: '¡Atención!',
        text: 'Se eliminara el Token de Acceso,',
        icon: 'info',
        showCancelButton: true,
        showConfirmButton: true,
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Confirmar',
    }).then((result) => {                   // Se agrega el método .then()
        if (result.isConfirmed) {           // Aquí se valida si se presionó 'Cancelar'
            
            var form = $('#deleted' + id);  
            
            $.ajax({
                type: "DELETE",
                url: form.attr('action') ,
                data: form.serialize(),  
                dataType: 'json',
                success: function (data) {

                    var Toastr = main();
                    Toastr.fire({
                        icon: 'success',
                        title: data,
                    });
                    
                $('tr[data-id="' + id + '"]').hide();
                
                }, error: function (data) {
                    var Toastr = main();
                    Toastr.fire({
                        icon: 'error',
                        title: data.responseJSON.message,
                    });
                }
            });
        }
    });
}

///validar formularios
$('#form-tokens').validate({
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
        event.preventDefault();
         $.ajax({
            type: form.method,
            url: form.action,
            data: $(form).serialize(),
            dataType: 'json',
            success: function (data) {
               
               var Toastr = main();
                Toastr.fire({
                    icon: 'success',
                    title: data[0],
                });
               $('#displaytoken').show();
               $('#newtoken').html(data[1]);

            }, error: function (data) {
                var Toastr = main();
                Toastr.fire({
                    icon: 'error',
                    title: data.responseJSON.message,
                });
            }
        });
    }
});
