<h1>Login form</h1>
{{ Form::open(array('action' => array('LoginController@performLogin', $subdomain), 'method' => 'POST')) }}
    {{ Form::label('email', 'Email:') }}
    {{ Form::text('email') }}
    {{ Form::label('password', 'Password:' ) }}
    {{ Form::password('password') }}
    {{Form::submit('Login') }}
{{ Form::close() }}
