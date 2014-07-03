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
            if (gettype($value)=='array')
                $new_config[$key] = join('|',$value);
            else
                $new_config[$key] = $value;

            $hehe = $new_config[$key];
            if (gettype($hehe)=='array')
                foreach ($new_config[$key] as $item)
                    foreach ($new_config as $key_new => $value_new)
                        $hehe = str_replace($key_new, $value_new, $hehe);
            else
                foreach ($new_config as $key_new => $value_new)
                    $hehe = str_replace($key_new, $value_new, $hehe);
            $new_config[$key] = $hehe;

            define($key, $hehe);
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
        // Session::end();                                                      // close session
    }
}