<?php

require 'model.php';

/**
* Controller class, it has some wrapper functions and leaves the logit to the developer.
*/
abstract class Controller
{
    /**
    * The body of the controller logic.
    */
    public abstract function Run();

    /**
    * Function to be executed when the user has been denied access to the page.
    */
    public function Denied()
    {
        Request::GotoAddress('/');
    }

    /**
    * Wrapper function to retrieve the class name.
    *
    * @return classname
    */
    public static function GetName()
    {
        return get_called_class();
    }

    /**
    * Wrapper function to run other funtions (anonymus).
    *
    * @return results of the called anonymus function (if any)
    */
    public function Exe($f)
    {
        return $f();
    }
}