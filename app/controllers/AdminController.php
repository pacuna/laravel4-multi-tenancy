<?php

class AdminController extends BaseController {

    public function createUser(){

        $subdomain = Input::get('subdomain');
        Event::fire('user.create', $subdomain);

    }

}
