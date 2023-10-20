Register form
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<form method="POST" action="/register">
    @csrf
    Name <input type="text" placeholder="name" name="name" required />
    <br>
    E-mail <input type="text" placeholder="myemail@domain.com" name="email" required />
    <br>
    Password <input type="password" placeholder="mypassword" name="password" required />
    <br>
    Password confirmation <input type="password" placeholder="mypassword" name="password_confirmation" required />
    <br>
    <input type="submit" value="Register" />
</form>
