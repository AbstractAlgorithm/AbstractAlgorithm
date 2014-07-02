<?php

require 'controller.php';

final class Request
{

    private static function getURI()
    {
        $uri = $_SERVER['REQUEST_URI'];                                         // get URI

        if (substr($uri, strlen($uri)-1,1)=='/')                                // strip ending '/'
            $uri = substr($uri, 0, strlen($uri)-1);

        if ($uri=='')                                                           // if it's index page
            $uri = '/index';

        return $uri;
    }

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

    private static function toCamelCase($uri)
    {
        $name = '';
        $part_array = preg_split('/(-|_)/', $uri);                              // split string into parts
        foreach ($part_array as $p)                                             // uppercase first letter
            $name .= ucfirst(strtolower($p));

        return $name;
    }

    private static function activateController($uri)
    {
        $name       = self::toCamelCase($uri);
        $classname= = $name.'Controller';										// generate class name
        $filename   = CTRL_DIR . $name  . '.php';                             	// generate filename to open
        
        @require $filename;
        @(new $classname())->run();                                             // execute controller
    }

    public static function route()
    {    
        $uri = self::getURI();                                                  // get path
        $uri = self::parseGET($uri);                                            // fill $_GET with params and strip them off uri

        if (Session::HasAccess($uri))
        {
            self::activateController($uri);
        }
    }

    public static function GotoAddress($address)
    {
        header('Location: '.$address);
    }

}