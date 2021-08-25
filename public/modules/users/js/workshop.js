var initLoginForm = function() {
    $('#inputEmailOrUsername').focus()
    $('#login').formValidation({
        framework: "bootstrap",
        excluded: [':disabled'],
        button: {
            selector: '#login-button',
            disabled: 'disabled'
        },
        icon: {
            valid: 'icon wb-check',
            invalid: 'icon wb-warning',
            validating: 'icon wb-warning'
        },
        fields: {
            email: {
                validators: {
                    notEmpty: {},
                    stringLength: {max: 30}
                }
            },
            password: {
                validators: {
                    notEmpty: {},
                    stringLength: {max: 20, min: 5}
                }
            }
        }
    }).on('success.form.fv', function(e) {
        e.preventDefault();
    }).on('err.field.fv', function(e, data) {
        data.element
            .data('fv.messages')
            .find('.help-block[data-fv-for="' + data.field + '"]').hide()
        ;
    });
    $('#login').submit(function(e) {
        var $form = $(e.target);
        var fv = $form.data('formValidation');
        if ( ! fv.isValid()) {
            return false;
        }
        fv.disableSubmitButtons(true);
        var errorText = 'Нешта не так...';
        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: $form.serialize(),
            success: function (result) {
                if (result.status != 'ok') {
                    toastr.warning(result.response, 'Увага!');
                    return false;
                }
                $(location).attr("href", result.response);
            },
            error: function(result) {
                toastr.error(result.status + ' - ' + result.statusText, errorText);
                if (result.status == 422) {
                    $.each(result.responseJSON, function(key, arr) {
                        $.each(arr, function(index, message) {
                            toastr.warning(message);
                        });
                    });
                }
            },
            complete: function(result) {
                fv.resetForm();
                fv.validate();
            }
        });
    });
};

var initRegisterForm = function() {
    $('#inputUsername').focus();
    $('#register').formValidation({
        framework: "bootstrap",
        excluded: [':disabled'],
        button: {
            selector: '#register-button',
            disabled: 'disabled'
        },
        icon: {
            valid: 'icon wb-check',
            invalid: 'icon wb-warning',
            validating: 'icon wb-warning'
        },
        fields: {
            username: {
                validators: {
                    notEmpty: {},
                    stringLength: {max: 20, min: 3},
                    regexp: {regexp: /^(([a-z\d]+)([\\_\-\.]*))*([a-z\d]+)$/i}
                }
            },
            email: {
                validators: {
                    notEmpty: {},
                    stringLength: {max: 30},
                    emailAddress: {}
                }
            },
            name: {
                validators: {
                    notEmpty: {},
                    stringLength: {max:30}
                }
            },
            password: {
                validators: {
                    notEmpty: {},
                    stringLength: {max: 20, min: 5}
                }
            },
            duplicated_password: {
                validators: {
                    notEmpty: {},
                    stringLength: {max: 20, min: 5},
                    identical: {field: 'password'}
                }
            },
            captcha: {
                validators: {
                    notEmpty: {},
                    stringLength: {max: 5},
                    regexp: {regexp: /[a-z\d]/i}
                }
            }
        }
    }).on('success.form.fv', function(e) {
        e.preventDefault();
    }).on('err.field.fv', function(e, data) {
        data.element
            .data('fv.messages')
            .find('.help-block[data-fv-for="' + data.field + '"]').hide()
        ;
    });
    $('#register').submit(function(e) {
        var $form = $(e.target);
        var fv = $form.data('formValidation');
        if ( ! fv.isValid()) {
            return false;
        }
        fv.disableSubmitButtons(true);
        var errorText = 'Нешта не так...';
        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: $form.serialize(),
            success: function (result) {
                if (result.status != 'ok') {
                    toastr.warning(result.response, 'Увага!');
                    return false;
                }
                $(location).attr("href", result.response);
            },
            error: function(result) {
                if (result.status == 422) {
                    $.each(result.responseJSON, function(key, arr) {
                        $.each(arr, function(index, message) {
                            toastr.warning(message);
                        });
                    });
                } else {
                    toastr.error(result.status + ' - ' + result.statusText, errorText);
                }
            },
            complete: function(result) {
                //grecaptcha.reset();
                fv.resetForm();
                fv.validate();
            }
        });
    });
};

var initForgotForm = function() {
    $('#inputEmail').focus();
    $('#recover').formValidation({
        framework: "bootstrap",
        excluded: [':disabled'],
        button: {
            selector: '#recover-button',
            disabled: 'disabled'
        },
        icon: {
            valid: 'icon wb-check',
            invalid: 'icon wb-warning',
            validating: 'icon wb-warning'
        },
        fields: {
            email: {
                validators: {
                    notEmpty: {},
                    stringLength: {max: 30},
                    emailAddress: {}
                }
            }
        }
    }).on('success.form.fv', function(e) {
        e.preventDefault();
    }).on('err.field.fv', function(e, data) {
        data.element
            .data('fv.messages')
            .find('.help-block[data-fv-for="' + data.field + '"]').hide()
        ;
    });
    $('#recover').submit(function(e) {
        var $form = $(e.target);
        var fv = $form.data('formValidation');
        if ( ! fv.isValid()) {
            return false;
        }
        fv.disableSubmitButtons(true);
        var errorText = 'Нешта не так...';
        $.ajax({
            url: $form.attr('action'),
            type: 'POST',
            data: $form.serialize(),
            success: function (result) {
                if (result.status != 'ok') {
                    toastr.warning(result.response, 'Увага!');
                    return false;
                }
                toastr.success('Пароль быў адпраўлены на Ваш email. Калі ласка, праверце.');
                $('#inputEmail').val('');
            },
            error: function(result) {
                if (result.status == 422) {
                    $.each(result.responseJSON, function(key, arr) {
                        $.each(arr, function(index, message) {
                            toastr.warning(message);
                        });
                    });
                } else {
                    toastr.error(result.status + ' - ' + result.statusText, errorText);
                }
            },
            complete: function(result) {
                fv.resetForm();
            }
        });
    });
};
