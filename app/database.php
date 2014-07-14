<?php


/**
* Singleton class that holds the connection to a database.
*/
final class DB
{

    private static $connection  = null;                                          // instance
    private static $config      = array();
    private static $db_name     = '';
    private static $db_user     = '';
    private static $db_pass     = '';
    private static $db_host     = '';

    public static function Assign($key, $value)
    {
        self::$config[$key] = $value;
    }

    /**
    * Connects to a database and holds that connection.
    */
    public static function Start()
    {
        if (self::$connection==null)
        {
            self::$connection = @mysqli_connect
                                (
                                    self::$config['DB_HOST'],
                                    self::$config['DB_USER'],
                                    self::$config['DB_PASS'],
                                    self::$config['DB_NAME']
                                );

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
    public static function Query($q)
    {
        if (self::$connection!=null)
            $res = mysqli_query(self::$connection, $q);
        else
            return null;

        $res_array = array();
        if(gettype($res)!='boolean')
            while($val = mysqli_fetch_array($res))
                $res_array[] = $val;
        else
            return $res;

        return $res_array;
    }

    public static function Conn()
    {
        return self::$connection;
    }
}