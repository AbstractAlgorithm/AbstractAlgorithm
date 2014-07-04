<?php


/**
* Class that is responsible for taking care about user creditentials and permissions.
* Alternative name - 'Auth'.
*/
final class Session
{
    /*
    * Variable that holds permission list.
    */
    private static $levels = array();

    /**
    * Setter for the Session::$levels that will be used for checking permission.
    *
    * @param key    name of the level
    * @param value  pages with no access to
    */
    public static function AddLevel($key, $value)
    {
        self::$levels[$key] = $value;
    }

    /**
    * Starts the session and initializes it with default parameters:<br>
    * no mail, no password, no username, lowest permissions
    */
    public static function Start()
    {
        session_start();
        $_SESSION['auth_data'] = array();
        $_SESSION['auth_data']['username'] = null;
        $_SESSION['auth_data']['password'] = null;
        $_SESSION['auth_data']['email'] = null;
        $_SESSION['auth_data']['level'] = 'LVL_VISITOR'; 
    }

    /**
    * Destroys the session and resets all the session data.
    */
    public static function End()
    {
        $_SESSION['auth_data']['username'] = null;
        $_SESSION['auth_data']['password'] = null;
        $_SESSION['auth_data']['email'] = null;
        $_SESSION['auth_data']['level'] = 'LVL_VISITOR'; 
        session_destroy();
    }

    /**
    * Tries to login user with given credentials.<br>
    * It updates session data if succeded.
    *
    * @param email      email fo login with
    * @param password   password to login with
    */
    public static function Login($email, $password)
    {
        $query =   'SELECT id, username, email, password 
                    FROM users 
                    WHERE email=\''.$email.'\' 
                    AND password=\''.sha1($password).'\'';

        $result = mysqli_query( $db_connection, $query );
        if($result)
        {    
            $_SESSION['auth_data']['username'] = $result[0]['username'];
            $_SESSION['auth_data']['password'] = $password;
            $_SESSION['auth_data']['email'] = $email;
            $_SESSION['auth_data']['level'] = 'LVL_VISITOR';                    // TODO : calc permissions
        }
    }

    /**
    * Tests if the current user has enough credentials to access the page.
    *
    * @param uri    page url that is to be tested
    * @return       result if user has the permission
    */
    public static function HasAccess($uri)
    {
        $has_no_access_to = self::$levels[$_SESSION['auth_data']['level']];
        if (preg_match('/'.$uri.'/i', $has_no_access_to))
            return false;    
        return true;
    }

    /**
    * Returns current session data.
    *
    * @return current session data
    */
    public static function GetData()
    {
        return $_SESSION['auth_data'];
    }
}