<style>
    #profile-update .avatar {
        width: 158px;
    }
    #profile-update .skype {
        color: #77bcfd;
        display: inline-block;
        font-size: 24px;
    }
    #profile-update .vk {
        color: #3d5a7d;
        display: inline-block;
        font-size: 24px;
    }
    #profile-update .facebook {
        color: #3B5998;
        display: inline-block;
        font-size: 24px;
    }
    #profile-update .twitter {
        color: #55ACEE;
        display: inline-block;
        font-size: 24px;
    }
    #profile-update .linkedin {
        color: #0976B4;
        display: inline-block;
        font-size: 24px;
    }
</style>


<form action="{{ route('workshop::users::update', ['user' => $user->getId()]) }}"
    method="post" class="form-horizontal" autocomplete="off" id="profile-update">

{!! Form::token() !!}

<div class="form-group">
    {!! Form::label('poster', 'Аватарка', array('class' => 'col-sm-3 control-label')) !!}
    <div class="col-sm-9" id="user-avatar">
        <img src="{{ $user->getAvatar()->getSrc() }}" />
        {!! Form::hidden(
            'avatar',
            $user->getAvatar()->getSrc(),
            ['class' => 'form-control', 'id' => 'avatar-hidden']
        ) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('name', 'Імя*', array('class' => 'col-sm-3 control-label')) !!}
    <div class="col-sm-9">
        {!! Form::text(
            'name',
            $user->getName(),
            ['class' => 'form-control', 'maxlength' => '30', 'id' => 'name']
        ) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('email', 'Email*', array('class' => 'col-sm-3 control-label')) !!}
    <div class="col-sm-9">
        {!! Form::email(
            'email',
            $user->getEmail(),
            ['class' => 'form-control', 'maxlength' => '30', 'id' => 'email']
        ) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('password', 'Пароль*', array('class' => 'col-sm-3 control-label')) !!}
    <div class="col-sm-9">
        {!! Form::input(
            'password',
            'password',
            $user->getAuthPassword(),
            ['class' => 'form-control', 'maxlength' => '20', 'id' => 'password']
        ) !!}
    </div>
</div>
<div class="form-group" id="form-group-year">
    {!! Form::label('country', 'Краіна', array('class' => 'col-sm-3 control-label')) !!}
    <div class="col-sm-9">
        {!! Form::text(
            'country',
            $user->getAddress()->getCountry(),
            ['class' => 'form-control', 'maxlength' => '40', 'id' => 'country']
        ) !!}
    </div>
</div>
<div class="form-group" id="form-group-year">
    {!! Form::label('city', 'Горад', array('class' => 'col-sm-3 control-label')) !!}
    <div class="col-sm-9">
        {!! Form::text(
            'city',
            $user->getAddress()->getCity(),
            ['class' => 'form-control', 'maxlength' => '40', 'id' => 'city']
        ) !!}
    </div>
</div>

@foreach (config('users.socialNetworks') as $id => $network)

    <div class="form-group">
        <label for="{{ $id }}" class="col-sm-3 control-label">
            <i class="icon bd-{{ $network['class'] }} {{ $network['class'] }}" aria-hidden="true"></i>
        </label>
        <div class="col-sm-9">
            {!! Form::text(
                $id,
                $user->getSocialProfile($id) ? $user->getSocialProfile($id)->getLink() : '',
                ['maxlength' => 200, 'class' => 'form-control', 'id' => $id, 'placeholder' => "Спасылка на профіль у {$network['name']}"]
            ) !!}
        </div>
    </div>

@endforeach

<div class="modal fade modal-slide-from-bottom" id="removeUserModal" aria-hidden="false" role="dialog" tabindex="-1" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <h4 class="modal-title">Вы ўпэўнены?</h4>
            </div>
            <div class="modal-body">
                <p>Пасля выдалення Вы не зможаце аднавіць свой профіль.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Не</button>
                <button type="button" class="btn btn-primary" id="removeUserConfirmed">Упэўнены</button>
            </div>
        </div>
    </div>
</div>

<div class="text-right">
    {!! Form::button('Выдаліць', [
        'type' => 'button',
        'class' => 'btn btn-danger',
        'id' => 'delete-profile'
    ]) !!}
    {!! Form::button('Захаваць', [
        'type' => 'submit',
        'class' => 'btn btn-primary',
        'id' => 'save-profile'
    ]) !!}
</div>

<script>
    (function() {

        $('#profile-update').formValidation({
            framework: "bootstrap",
            excluded: [':disabled'],
            button: {
                selector: '#save-profile',
                disabled: 'disabled'
            },
            icon: {
                valid: 'icon wb-check',
                invalid: 'icon wb-warning',
                validating: 'icon wb-warning'
            },
            fields: {
                name: {
                    validators: {
                        notEmpty: {
                            message: 'Увядзіце імя'
                        },
                        stringLength: {
                            message: 'Скараціце да 30 сімвалаў',
                            max: 30
                        }
                    }
                },
                email: {
                    validators: {
                        notEmpty: {
                            message: 'Увядзіце email'
                        },
                        stringLength: {
                            message: 'Скараціце да 20 сімвалаў',
                            max: 30
                        },
                        emailAddress: {
                            message: 'Увядзіце слушны email адрас'
                        }
                    }
                },
                password: {
                    validators: {
                        notEmpty: {
                            message: 'Увядзіце пароль'
                        },
                        stringLength: {
                            message: '5 - 20 сімвалаў',
                            max: 20,
                            min: 5
                        }
                    }
                },
                country: {
                    validators: {
                        stringLength: {
                            message: 'Скараціце да 40 сімвалаў',
                            max: 40
                        }
                    }
                },
                city: {
                    validators: {
                        stringLength: {
                            message: 'Скараціце да 40 сімвалаў',
                            max: 40
                        }
                    }
                },
                vk: {
                    validators: {
                        stringLength: {
                            message: 'Скараціце да 200 сімвалаў',
                            max: 200
                        },
                        uri: {
                            message: 'Увядзіце слушны адрас'
                        }
                    }
                },
                fb: {
                    validators: {
                        stringLength: {
                            message: 'Скараціце да 200 сімвалаў',
                            max: 200
                        },
                        uri: {
                            message: 'Увядзіце слушны адрас'
                        }
                    }
                },
                tw: {
                    validators: {
                        stringLength: {
                            message: 'Скараціце да 200 сімвалаў',
                            max: 200
                        },
                        uri: {
                            message: 'Увядзіце слушны адрас'
                        }
                    }
                },
                ln: {
                    validators: {
                        stringLength: {
                            message: 'Скараціце да 200 сімвалаў',
                            max: 200
                        },
                        uri: {
                            message: 'Увядзіце слушны адрас'
                        }
                    }
                },
                s: {
                    validators: {
                        stringLength: {
                            message: 'Скараціце да 100 сімвалаў',
                            max: 100
                        }
                    }
                }
            }
        }).on('success.form.fv', function(e) {
            e.preventDefault();
        });

        $('#profile-update').submit(function(e) {
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
                success: function(result) {
                    if ( ! result.status || result.status != 'ok') {
                        toastr.warning(result.response, errorText);
                        return false;
                    }
                    toastr.success(result.response);
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
                complete: function() {
                    fv.resetForm();
                    fv.disableSubmitButtons(false);
                }
            });
        });

        var destroyUrl = '{{ route('workshop::users::remove', ['user' => $user->getId()]) }}';

        $('#delete-profile').on('click', function () {
            var modal = $('#removeUserModal');
            modal.modal('toggle');
            $(modal).find('#removeUserConfirmed').on('click', function() {
                var errorText = 'Нешта не так';
                $.ajax({
                    url: destroyUrl,
                    type: 'GET',
                    success: function(result) {
                        if (result.status && result.status == 'ok') {
                            $(location).attr("href", result.response);
                        } else {
                            toastr.warning(result.response);
                        }
                    },
                    error: function(result) {
                        toastr.error(result.status + ' - ' + result.statusText, errorText);
                        if (result.status == 422) {
                            $.each(result.responseJSON, function(key, arr) {
                                $.each(arr, function(index, message) {
                                    toastr.error(message);
                                });
                            });
                        }
                    }
                });
            });
        });

    })();
</script>

</form>
