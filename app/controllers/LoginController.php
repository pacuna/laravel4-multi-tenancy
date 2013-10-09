<?php

class LoginController extends BaseController {

    public function showLoginForm($subdomain){
        $data['subdomain'] = $subdomain;
        return View::make('login.loginform', $data);
    }

    public function performLogin($subdomain){

        $email = Input::get('email');
        $password = Input::get('password');

        //laravel auth
        if(Auth::attempt(array('email' => $email, 'password' => $password)))
        {
            return View::make('users.dashboard')
                ->with('email', Auth::user()->email);
        }
        else
        {
            return 'problema al realizar el login';
        }
    }

    public function performLogout($subdomain=''){
        Auth::logout();

        return Redirect::route('login', array('subdomain' => $subdomain));
    }
}
