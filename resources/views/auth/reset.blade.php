<!-- resources/views/auth/reset.blade.php -->

<form method="POST" action="/password/reset">
    {!! csrf_field() !!}
    <input type="hidden" name="token" value="{{ $token }}">

    @if (count($errors) > 0)
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <div>
        电子邮箱
        <input type="email" name="email" value="{{ old('email') }}">
    </div>

    <div>
        密码
        <input type="password" name="password">
    </div>

    <div>
        重新输入密码
        <input type="password" name="password_confirmation">
    </div>

    <div>
        <button type="submit">
            重置密码
        </button>
    </div>
</form>