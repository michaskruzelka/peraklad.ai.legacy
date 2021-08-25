@extends('users::layouts.auth')

@section('body-class') page-login-v2 @endsection

@section('form')
    <h3 class="font-size-24">Уваход</h3>
    <p>Калі ласка, запоўніце абавязковыя палі, каб увайсці ў майстэрню субтытраў.</p>
    <form method="post" id="login" action="{{ route('users::auth::doLogin') }}">
        <div class="form-group">
            <label class="sr-only" for="inputEmailOrUsername">Email або username</label>
            <input type="text" maxlength="30" class="form-control" id="inputEmailOrUsername" name="email" placeholder="Email або username">
        </div>
        <div class="form-group">
            <label class="sr-only" for="inputPassword">Пароль</label>
            <input type="password" maxlength="20" class="form-control" id="inputPassword" name="password" placeholder="Пароль">
        </div>
        <div class="form-group clearfix">
            <div class="checkbox-custom checkbox-inline checkbox-primary pull-left">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Запомніць мяне</label>
            </div>
            <a class="pull-right" href="{{ route('users::auth::forgot') }}">Забыліся пароль?</a>
        </div>
        <button type="submit" id="login-button" class="btn btn-primary btn-block">Увайсці</button>
    </form>
    <p>Няма акаўнта? <a href="{{ route('users::auth::register') }}">Стварыце</a></p>

    <script>
        initLoginForm();
    </script>
@endsection
