$(document).ready(function () {
    $(document).on("input", ".input-number", function () {
        this.value = this.value.replace(/[^0-9]/g, "");
    });

    $("#mes")
        .datetimepicker({
            format: "MM/YYYY",
            maxDate: moment(),
            viewMode: "months", // para que solo muestre meses y años
            // opcional: ignoreReadonly: true, si el input es readonly y quieres que funcione
        })
        .on("change", function (e) {
            UpdateDisplayAll();
        });

    $(".connectedSortable").sortable({
        placeholder: "sort-highlight",
        connectWith: ".connectedSortable",
        handle: ".card-header, .nav-tabs",
        forcePlaceholderSize: true,
        zIndex: 999999,
    });
    $(".connectedSortable .card-header").css("cursor", "move");

    // jQuery UI sortable for the todo list
    $(".todo-list").sortable({
        placeholder: "sort-highlight",
        handle: ".handle",
        forcePlaceholderSize: true,
        zIndex: 999999,
    });
});
