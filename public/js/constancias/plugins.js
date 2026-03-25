if ($('.dateter').length != 0) {
    $('.dateter').daterangepicker({
        "locale": {
            "format": "DD/MM/YYYY",
        },
        //minDate: moment().add(-1, 'month'),
        //maxDate: moment().add(2, 'month'),
    });
}