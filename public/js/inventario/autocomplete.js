if ($('.autocomplete').length != 0) {
    $(function () {
        /// autocomplete de marcas
        $('.autocomplete').each(function () {
            var input = $(this);
            var campoId = input.attr('id'); // Obtener el ID del campo            
            input.autocomplete({
                source: function (request, response) {
                    $.ajax({
                        url: $('#rutaautocomplete').val(),
                        dataType: 'json',
                        type: "POST",
                        data: {
                            _token: $("input[name=_token]").val(),
                            campo: campoId,
                            componente: $('#componente').val(), /// para el uso de creacion de componentes
                            stock: $('#asignasede').val(), /// vista de asignacion de componentes en equipos
                            termino: request.term
                        },
                        success: function (data) {
                            if (data.length === 0) {
                                $(':input[type="submit"]').prop('disabled', true);
                                data.push({label: "No existen criterios de búsqueda", value: ""});
                            } else {
                                $(':input[type="submit"]').prop('disabled', false);
                            }
                            response(data);
                        }
                    });
                }
            });
        });
    });
}