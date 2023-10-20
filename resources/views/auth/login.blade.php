@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<form method="POST" action="/login">
    @csrf

    {{ trans('auth.E-mail') }} <input type="text" placeholder="myemail@domain.com" name="email" required />
    <br>
    {{ trans('auth.Password') }} <input type="password" placeholder="mypassword" name="password" required />
    <br>
    {{ trans('auth.Remember') }} <input type="checkbox" name="remember" />
    <br>
    <input type="submit" value="{{ trans('auth.Login') }}" />
</form>
