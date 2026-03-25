  $(function () {

    $('.table').on('click', '.viewhistory', function (event) {
        event.preventDefault();

        $("#name").text($(this).data("name"));

        $('#viewhistory').modal('show');
    });

});


$("#close-history").click(function () {

  if ($.fn.dataTable.isDataTable('#datatable-historial')) {
            $('#datatable-historial').DataTable().destroy();
        }

});