<?php

require 'controller.php';

/**
* Class that handles the request and decides what is next to be executed.<br>
* It takes careof the URL format and parameters.
*/
final class Request
{
    private static $uri;
    /**
    * Stylizes the requested URI to the uniform format.
    * 
    * @return formatted uri
    */
    private static function getURI()
    {
        $uri = $_SERVER['REQUEST_URI'];                                         // get URI

        $uri = str_replace('../', '', $uri);                                    // security reasons

        if (substr($uri, strlen($uri)-1,1)=='/')                                // strip ending '/'
            $uri = substr($uri, 0, strlen($uri)-1);

        if ($uri=='')                                                           // if it's index page
            $uri = '/index';

        return $uri;
    }

    /**
    * Parses the URL, stripping the GET parameters and returning clean version of the URL.
    *
    * @param uri 	URL to parse
    * @return 		clean version of the URL
    */
    private static function parseGET($uri)
    {
        preg_match('/^\/([^\/]+)/', $uri, $match_stripped);                     // get the filename
        $stripped = $match_stripped[1];

        $uri = substr($uri, strlen($stripped)+1);                               // set GET parameters from url
        preg_match_all('/\/([^\/]*)\/([^\/]*)/', $uri, $params);                // match /name/val
        for ($i=0; $i<count($params[1]); $i++)                                  // add to $_GET
            $_GET[ $params[1][$i] ] = $params[2][$i];

        return $stripped;
    }

    /**
    * Converts this-IS_liNk to ThisIsLink camel-case format.
    *
    * @param uri 	unformatted version of the URL
    * @return 		camel-case version
    */
    private static function toCamelCase($uri)
    {
        $name = '';
        $part_array = preg_split('/(-|_)/', $uri);                              // split string into parts
        foreach ($part_array as $p)                                             // uppercase first letter
            $name .= ucfirst(strtolower($p));

        return $name;
    }

    /**
    * Requests for the controller's file and executes it.
    *
    * @param uri 	controller to be requested
    * @see 			Controller
    */
    private static function activateController($uri)
    {
        $name       = self::toCamelCase($uri);
        $classname  = $name.'Controller';                                       // generate class name
        $filename   = CTRL_DIR.'/'.$name .'.php';                             	// generate filename to open
        
        if (file_exists($filename))
        {
            require $filename;
            if (Session::HasAccess($uri))
            {
                (new $classname())->Run();                                      // execute controller
            }
            else
            {
                (new $classname())->Denied();
            }
        }
        else                                                                    // TODO : redirect to 404 page
        {
            self::GotoAddress('/');
        }
        
    }

    /**
    * Main function for handling requests. Parses the URL and executes controller.
    */
    public static function Route()
    {    
        $uri        = self::getURI();                                           // get path
        $uri        = self::parseGET($uri);                                     // fill $_GET with params and strip them
        self::$uri  = $uri;

        self::activateController($uri);
    }

    /**
    * It will redirect user to specified page immediately.
    *
    * @param address 	address wo which to redirect
    */
    public static function GotoAddress($address)
    {
        header('Location: '.$address);
        die();
    }

    public static function URI()
    {
        return self::$uri;
    }

    public static function GET()
    {
        if(func_num_args()==1)
            return $_GET[func_get_arg(0)];
        return $_GET;
    }

    public static function POST()
    {
        if(func_num_args()==1)
            return $_POST[func_get_arg(0)];
        return $_POST;
    }
}