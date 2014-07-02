<?php


/**
* Singleton class that holds the connection to a database.
*/
final class DB
{

    private static $connection = null;                                          // instance

    /**
    * Connects to a database and holds that connection.
    */
    public static function Start()
    {
        if (self::$connection==null)
        {
            self::$connection = @mysqli_connect('localhost','root','','test');  // connects

            if (mysqli_connect_errno())                                         // connection failed?
            {
                echo "Failed to connect to MySQL: " . mysqli_connect_error();
                self::$connection = null;
                die();
            }
        }

    }

    /**
    * Closes a connection with a database.
    */
    public static function End()
    {
        if (self::$connection!=null)                                            // close a connection if it's connected
            mysqli_close(self::$connection);
    }

    /**
    * It executes a MySQL query.
    *
    * @param q  query to execute
    * @return   array that holds information
    */
    public static function query($q)                                            
    {
        return (self::$connection!=null)
                ? mysqli_query(self::$connection, $q)
                : array();
    }

    /**
    * Itterate function.
    * 
    * @param res    query to itterate though
    * @return       array
    */
    public static function GetNext($res)
    {
        return ($res && count($res)>0)
                ? mysqli_fetch_array($res)
                : array();
    }
}