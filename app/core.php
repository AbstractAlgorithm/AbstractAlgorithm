<?php

require 'session.php';
require 'database.php';
require 'template.php';
require 'request.php';

/**
* Bootstrap class for the entire application. It does all the init, it<br>
* delegates the request for handling and in the end it destroys everything.
*/
final class Core
{
    public static $config = array();
    
    /**
    * Loads configuration file and creates constants from it.
    */
    private static function initConfig()
    {
        $config_str = file_get_contents("app/config.json");
        $config     = json_decode($config_str,true);
        $new_config = array();

        foreach ($config as $key => $value)
        {
            $expanded = gettype($value)=='array' ? join('|',$value) : $value;   // concatenate to one string

            foreach ($new_config as $key_ => $value_)                           // use previously defined config
                $expanded = str_replace($key_, $value_, $expanded);

            $new_config[$key] = $expanded;

            define($key, $expanded);                                            // define to be globally accessible

            if (preg_match('/^LVL_|^NO_ACCESS_/i', $key))                       // add to Session::levels config
                Session::AddLevel($key, $expanded);
            else if (preg_match('/^DB_/', $key))
                DB::Assign($key, $expanded);
            else                                                                // add to Core::config data
                self::$config[$key] = $expanded;
        }
    }

    /**
    * Main entry point of the application.
    */
    public static function Start()
    {
        self::initConfig();                                                     // get configuration data
        DB::Start();                                                            // init database
        Session::Start();                                                       // init session

        Request::Route();                                                       // handle request
        
        DB::End();                                                              // close database
        // Session::End();                                                      // close session
    }
}