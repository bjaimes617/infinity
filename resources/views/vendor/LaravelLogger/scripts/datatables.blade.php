
<script type="text/javascript" src="{{config('LaravelLogger.loggerDatatablesinit')}}"></script>
<script type="text/javascript" src="{{config('LaravelLogger.loggerDatatablesJSboots')}}"></script>
<script type="text/javascript" src="{{config('LaravelLogger.loggerDatatablesresponsible')}}"></script>
<script type="text/javascript" src="{{asset('/plugins/datatables-scroller/js/dataTables.scroller.js')}}"></script>
<script type="text/javascript">
    $(function() {
        $('.data-table').dataTable({            
            "order": [[0]],            
            "pageLength": 20,
            "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
            
            //"deferRender": true,
            "processing": true,
            "deferRender": true,
            
            "paging": true,         
            "lengthChange": true,
            "searching": true,
            "ordering": false,
            "info": true,
            "autoWidth": true,
            "responsive": true,
           "dom": 'rtS',
            "sPaginationType": "full_numbers",
            'aoColumnDefs': [{
                'bSortable': false,
                'searchable': false,
                'aTargets': ['no-search'],
                'bTargets': ['no-sort']
            }]
        });
    });
</script>