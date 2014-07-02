<?php


/**
* Class that is responsible for taking care about user creditentials and permissions.
* Alternative name - 'Auth'.
*/
class Session
{
    public static function Start()
    {
        session_start();
    }

    public static function end()
    {

        if(isset($_SESSION['auth_data']))
            unset($_SESSION['auth_data']);
        
        session_destroy();
    }

    public static function login($email, $password)
    {
        $mysql_query_string = 'SELECT id, username, email, password FROM users WHERE email=\''.$email.'\' AND password=\''.sha1($password).'\'';
        $result = mysqli_query( $db_connection, $mysql_query_string );
        if($result)
        {    
            $_SESSION['auth_data'] = array();
            $_SESSION['auth_data']['username'] = $result[0]['username'];
            $_SESSION['auth_data']['password'] = $password;
            $_SESSION['auth_data']['email'] = $email;
        }
    }

    public static function getUserData()
    {
        $data = $_SESSION['auth_data'];
        return $data;
    }

    public static function HasAccess()
    {
        return true;                                                            // TODO : check config
    }

    public static function hasAdminAccess() {
        return $_SESSION['admin'];
    }

}