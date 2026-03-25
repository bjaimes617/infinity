
$.validator.setDefaults({
    debug: false,
    success: "valid"
});

var validation = function () {

    var password = function () {
        $.validator.addMethod("pwcheck", function (value) {
            return /^[A-Za-z0-9\d=!\-@._*]*$/.test(value) // consists of only these
                    && /[A-Z]/.test(value) // has a lowercase letter
                    && /\d/.test(value) // has a digit
        });

        $("#form_update_password").validate({
            rules: {
                password: {
                    required: true,
                    pwcheck: true,
                    minlength: 8,
                    remote: {
                        url: $("#password").data('check'),
                        type: "post",
                        data: {_token: $("input[name=_token]").val()}
                    }
                },
                confirmpassword: {
                    required: true,
                    equalTo: "#password"
                }
            },
            messages: {
                password: {
                    pwcheck: 'La contraseña ingresada no cumple con las condiciones de seguridad.',
                    required: 'Este campo es obligatorio.',
                    remote: 'La contraseña nueva no puede ser igual a las ultimas tres (03) que utilizaste.',
                    minlength: 'Debe ingresar como minimo 8 caracteres.'
                },
                confirmpassword: {
                    equalTo: 'La contraseña debe coincidir con la que ingreso.',
                    required: 'Este campo es obligatorio.'
                }
            },
            highlight: function (element) {
                $(element).closest('.form-control').addClass('is-invalid');
            },
            unhighlight: function (element) {
                $(element).closest('.form-control').removeClass('is-invalid');
            },
            errorPlacement: function (error, element) {
                if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function (form) {
    form.submit();
            }
        });
    };
    return {
        // public functions
        init: function () {
            password();
        }
    };
}();


$(document).ready(function () {
    validation.init();
});


function nobackbutton() {

    window.location.hash = "no-back-button";
    window.location.hash = "Again-No-back-button"; //chrome	
    window.onhashchange = function () {
        window.location.hash = "no-back-button";
    }

}