<h1>Add new user</h1>

{{ Form::open(array('url' => '/admin/users/create', 'method'=>'POST')) }}
    {{ Form::label('subdomain', 'Subdomain: ') }}
    {{ Form::text('subdomain') }}
    {{ Form::submit('Add new subdomain') }}
{{ Form::close() }}