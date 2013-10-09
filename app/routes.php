<?php

$subdomain = '';
$domain_parts = explode('.', Request::server('HTTP_HOST'));
if (count($domain_parts) == 3) {
    $subdomain = $domain_parts[0];

    if ($subdomain == 'www') {
        $subdomain = '';
    }
}
if (empty($subdomain)) {
    //create routes meant for webapp.com or www. webapp.com
    Route::get('/', 'IndexController@showIntro');
} else {
    //create routes meant for tenant.webapp.com
    Route::group(array('domain' => '{subdomain}.'.Config::get('app.domain'), 'before' => 'db.setup'), function() {
        //login, logout
        Route::get('/', array(
            'as' => 'login',
            'uses' => 'LoginController@showLoginForm'
        ));
        Route::post('login', array(
            'as' => 'performLogin',
            'uses' => 'LoginController@performLogin'
        ));
        Route::get('logout', array(
            'as' => 'logout',
            'uses' => 'LoginController@performLogout'
        ));
    });

    //routes that require login
    //REMEMBER that the db.setup filter must be run first in order to change the database to the tenant's so that your sessions will be read from the correct database
    Route::group(array('domain' => '{subdomain}.'.Config::get('app.domain'), 'before' => 'db.setup|auth'), function() {
        Route::get('dashboard', array(
            'as' => 'dashboard',
            'uses' => 'DashboardController@index'
        ));
    });
}

Route::filter('db.setup', function($route, $request) {
    $host = $request->getHost();
    $parts = explode('.', $host);
    $subdomain = $parts[0];
    $tenant = Tenant::where('subdomain', '=', $subdomain)->first();

    //unable to find tenant in database, redirect to myapp.com
    if ($tenant == null) return Redirect::to('http://'.Config::get('app.domain'));

    //set the default database connection to the tenant database
    Config::set('database.connections.mysql_tenant.database', $subdomain);
    DB::setDefaultConnection('mysql_tenant');
});


//Admin routes
Route::group(array('prefix' => 'admin'), function()
{
    Route::get('/users/create', function(){
        return View::make('admin.users.create');
    });

    Route::post('/users/create', 'AdminController@createUser');

});
