@extends('users::layouts.auth')

@section('body-class') page-login-v2 @endsection

@section('form')
    <style>
        .g-recaptcha {margin-left: 23px;}
    </style>
    <h3 class="font-size-24">Рэгістрацыя</h3>
    <p>Калі ласка, запоўніце абавязковыя палі, каб стварыць акаўнт.</p>
    <form method="post" id="register" action="{{ route('users::auth::doRegister') }}">
        <div class="form-group">
            <label class="sr-only" for="inputUsername">Username</label>
            <input type="text" maxlength="20" class="form-control" id="inputUsername" name="username" placeholder="Username (мянушка лацінскімі літарамі)">
        </div>
        <div class="form-group">
            <label class="sr-only" for="inputEmail">Email</label>
            <input type="text" maxlength="30" class="form-control" id="inputEmail" name="email" placeholder="Email">
        </div>
        <div class="form-group">
            <label class="sr-only" for="inputName">Імя</label>
            <input type="text" maxlength="30" class="form-control" id="inputName" name="name" placeholder="Сапраўднае імя і прозвішча">
        </div>
        <div class="form-group">
            <label class="sr-only" for="inputPassword">Пароль (5-20 сімвалаў)</label>
            <input type="password" maxlength="20" class="form-control" id="inputPassword" name="password" placeholder="Пароль (5-20 сімвалаў)">
        </div>
        <div class="form-group">
            <label class="sr-only" for="inputDuplicatedPassword">Паўтарыце пароль</label>
            <input type="password" maxlength="20" class="form-control" id="inputDuplicatedPassword" name="duplicated_password" placeholder="Паўтарыце пароль">
        </div>
        {!! captcha_img() !!}<br /><br />
        <div class="form-group">
            <label class="sr-only" for="captcha">Увядзіце лічбы з малюнка</label>
            <input type="text" maxlength="5" name="captcha" class="form-control" placeholder="Увядзіце лічбы з малюнка">
        </div>
        <button type="submit" id="register-button" class="btn btn-primary btn-block">Стварыць</button>
    </form>
    <p>Ужо маеце акаўнт? <a href="{{ route('users::auth::login') }}">Увайдзіце</a></p>

    <script>
        initRegisterForm();
    </script>
@endsection
