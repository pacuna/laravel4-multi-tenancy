<?php namespace Infrastructure\Handlers;
use Tenant;
use Config;
use DB;
use Schema;
use Hash;

class UserEventHandler {

    public function onCreate($subdomain)
    {
        //first create the new tenant using the default connection
        Config::set('database.default', 'mysql');
        $newTenant = new Tenant;
        $newTenant->subdomain = $subdomain;
        $newTenant->save();

        //now we have to create a new db for the tenant
        $link = mysql_connect('localhost', 'root', 'bk999pv');
        if (!$link) {
            die('Could not connect: ' . mysql_error());
        }

        //TODO: FIX THIS QUERY!!
        $sql = 'CREATE DATABASE '.$subdomain;
        if (mysql_query($sql, $link)) {
        //Database subdomain created successfully

        //now we make a connection to the new database
            Config::set('database.connections.mysql_tenant.database', $subdomain);

            //set the default connection
            DB::setDefaultConnection('mysql_tenant');

            //create a table for users in the database
            Schema::create('users', function($table)
            {
                $table->increments('id');
                $table->string('username');
                $table->string('email');
                $table->string('password');
                $table->timestamps();
            });

            //finally insert a record for the admin user
            //user: admin_subdomain, pass: admin, email: subdomain@example.com
            DB::table('users')->insert(
                array(
                    'username' =>  'admin_'.$subdomain,
                    'email'    =>  $subdomain.'@example.com',
                    'password' =>  Hash::make('admin')
                ));

            //next we restore the default connection
            Config::set('database.default', 'mysql');

            return 'al parecer todo ha salido bien';

        } else {
            echo 'Error creating database: ' . mysql_error() . "\n";
        }
    }

    public function subscribe($events)
    {
        $events->listen('user.create', 'Infrastructure\Handlers\UserEventHandler@onCreate');
    }
}