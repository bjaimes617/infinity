$(document).ready(function () {

    $(function () {
        var $calendarLi = $('li.nav-item.calendar');
        var url = $('li.nav-item.calendar').data('href');
        // console.log("ejecutando", url);
        $.ajax({
            type: 'POST',
            url: url,
            data: { _token: $("input[name=_token]").val() },
            dataType: 'json',
            success: function (data) {
                if (data === 0) {
                    $calendarLi.find('span.badge.badge-info.navbar-badge').removeClass('badge-info');
                    $calendarLi.find('span.badge.navbar-badge').addClass('badge-success');
                    $calendarLi.find('span.badge.badge-success.navbar-badge').text(data);
                } else {
                    $calendarLi.find('span.badge.badge-info.navbar-badge').text(data);
                }

            }
        });
    });
});