<script>   
    var KTDatatablesSearchOptionsAdvancedSearch = function () {
        $.fn.dataTable.Api.register('column().title()', function () {
            return $(this.header()).text().trim();
        });
        var initTable1 = function () {
            // begin first table
            $('#Tablepersonal').DataTable({
                responsive: true,
                "ordering": false,
                "autoWidth": false,
                "deferRender": true,
                "bLengthChange": false,            
                //pageLength: 6,
                processing: true,
                serverSide: false,
                ajax: {
                    url: "{{ route('evaluacion.bring.personal',[$skill_asig,$cargo_eval,$fecha_evaluacion,$tipo_periodo,$cartera,$area_sel]) }}",
                    type: 'POST',
                    data: {
                        _token: $("input[name='_token']").val(),
                        columnsDef: [
                            "Cedula",
                            "Nombre",
                            "Cargo",
                            "Skill",
                            "Acciones"],
                    },
                },
                columns: [
                    {data: "Cedula"},
                    {data: "Nombre"},
                    {data: "Cargo"},
                    {data: "Skill"}, 
                    {data: "Acciones"}
                ],
            });
        };
        return {
            //main function to initiate the module
            init: function () {
                initTable1();
            }
        };
    }();

    jQuery(document).ready(function () {
        KTDatatablesSearchOptionsAdvancedSearch.init();
    });
</script>