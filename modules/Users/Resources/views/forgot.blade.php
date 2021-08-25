@extends('users::layouts.auth')

@section('body-class') page-login-v2 @endsection

@section('form')
    <h3 class="font-size-24">Забыліся свой пароль?</h3>
    <p>Калі ласка, увядзіце свой email, на які мы адправім адноўлены пароль.</p>
    <form method="post" id="recover" action="{{ route('users::auth::recover') }}">
        <div class="form-group">
            <label class="sr-only" for="inputEmail">Email</label>
            <input type="text" maxlength="30" class="form-control" id="inputEmail" name="email" placeholder="Email">
        </div>
        <button type="submit" id="recover-button" class="btn btn-primary btn-block">Аднавіць</button>
    </form>
    <p>Успомнілі? <a href="{{ route('users::auth::login') }}">Увайдзіце</a></p>

    <script>
        initForgotForm();
    </script>
@endsection
