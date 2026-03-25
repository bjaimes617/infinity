$(document).ready(function () {
      
    $(function () {
    if ($('#permisos_table').length != 0) {
        $("#permisos_table").DataTable({
            "lengthMenu": [15, 20, 15, 30, 50, 60],
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "ordering": true,
            //"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        });
    }
});

    $(function () {
        if ($('.duallistbox').length != 0) {
            $('.duallistbox').bootstrapDualListbox({
                nonSelectedListLabel: 'No Seleccionadas',
                selectedListLabel: 'Seleccionadas',
                preserveSelectionOnMove: 'Mover',        
                infoText:"Opciones {0}",
                infoTextEmpty:"Vacio ",
                moveOnSelect: false
            }); 
        };
       
    });

    if ($('.select2').length != 0) {
        $(".select2").select2({
            'placeholder': "[Seleccione una o mas Opciones]",
            'minimumInputLengthopcion': 0,
            'minimumResultsForSearch': 0,
        });
        // con esto forzamos que el selector no se envie vacio al ser un selector multiple o individual      
    }       
});

$('#autorization_create').validate({
    lang: 'es',    
    errorElement: 'span',    
    errorPlacement: function (error, element) {       
        error.addClass('is-invalid text-sm');
        element.closest('.form-group').append(error);      
    },
    highlight: function (element, errorClass, validClass) {
        $(element).addClass('is-invalid','error');
    },
    unhighlight: function (element, errorClass, validClass) {
        $(element).removeClass('is-invalid','error');
    },
    submitHandler: function (form) {
        
       if($('#skill').val().length === 0 ||$('#skill').val().length === null ){
            var Toastr = main();
            Toastr.fire({
                icon: 'error',
                title: "Debe Seleccionar al menos un Skill a autorizar.",
            });
       } else {
             Swal.fire({
                title: "Confirmación de Accion",
                html:"¿Esta de acuerdo en autorizar el acceso al(los) Skill(s) seleccionado(s) la usuario?",
                showDenyButton: false,
                showCancelButton: true,
                confirmButtonText: "Procesar",
                denyButtonText: "Regresar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    form.submit();
                }
            });
       }
    }
});

$('#area').on('change', function () {            
    var url = $(this).data('href');     
    var data= {
        autorize:true,
        area : $(this).val()  ,
        _token : $("input[name='_token']").val()
    };
    $('#cartera').empty();
    $('#cartera').bootstrapDualListbox({removeSelectedLabel: 'Remove selected', 'refresh': true});
    
    $.ajax({
        type: 'POST',
        url: url,
        data: data,
        dataType: 'json',
        success: function (data)
        {
            for (i = 0; i < data.length; i++)
            {
                $('#cartera').append($("<option></option>").attr("value", data[i].id).text(data[i].nombre_cartera));
            }
            $('#cartera').bootstrapDualListbox('refresh', true);
        }
    });
    $(this).bootstrapDualListbox('refresh', true);
});

$('#cartera').on('change', function () {    
    $(this).bootstrapDualListbox('refresh', true);    
    var url = $(this).data('href');    
    var data = {
        autorize:true,
       cartera : $(this).val(),
        _token : $("input[name='_token']").val(),
    };
    $('#skill').empty();
    $('#skill').bootstrapDualListbox({removeSelectedLabel: 'Remove selected', 'refresh': true});
    $.ajax({
        type: 'POST',
        url: url,
        data: data,
        dataType: 'json',
        success: function (data)
        {
            for (i = 0; i < data.length; i++)
            {
                $('#skill').append($("<option></option>").attr("value", data[i].id).text(data[i].nombre_skill));
            }
            $('#skill').bootstrapDualListbox('refresh', true);
        }
    });
});

$('#skill').on('change', function () {
    $(this).bootstrapDualListbox('refresh', true);
});

///destruir usuarios
function Destroy(id){  
    Swal.fire({
        title: '¿Esta seguro en eliminar esta autorizacion de Skills?, Tenga en cuenta, que el usuario no podra acceder a los datos',
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
        
        if ($.fn.dataTable.isDataTable('#PermissionsUsers')) {
            $('#PermissionsUsers').DataTable().destroy();
        }    
            $('#PermissionsUsers').DataTable({
                "initComplete": function (settings, json) {

                },
                "responsive": true,
                "ordering": true,               
                "autoWidth": false,
                "data": data,
                "lengthMenu": [10, 20, 50, 75],
                "bPaginate": true,
                "bProcessing": true,
                "columns": [
                    {mData: 'campania'},
                    {mData: 'cartera'},                  
                    {mData: 'skill'},                  
                ],
                order: [                   
                    [1, 'asc']
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

