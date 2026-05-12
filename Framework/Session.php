<?php
namespace Framework;


class Session
{   
    /**
     * start a new session
     * @return void
     */
    public static function start(){
        if(session_status() === PHP_SESSION_NONE){
            session_start();
        }
    }

    /**
     * set a session key:val pair
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set($key, $value){
        $_SESSION[$key] = $value;
    }

    /**
     * return a session val by key
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null){
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }

    /**
     * check if session key exists
     * @param string $key
     * @return boolean
     */
    public static function has($key){
        return isset($_SESSION[$key]);
    }

    /**
     * clear session by key
     * @param string $key
     * @return void
     */
    public static function clear($key){
        if(isset($_SESSION[$key])){
            unset($_SESSION[$key]);
        }
    }

    /**
     * clear all sessions
     * @return void
     */
    public static function clearAll(){
        session_unset();
        session_destroy();
    }

    /**
     * set flash message
     * @param string $key
     * @param string $message
     * @return void
     */
    public static function setFlashMessage($key, $message){
        self::set($key, $message);
    }

    /**
     * return a flash message
     * @param string $key
     * @param mixed $default
     * @return string
     */
    public static function getFlashMessage($key, $default= null){
        $message = self::get($key, $default);
        self::clear($key);
        return $message;
    }


    /*
    $_SESSION["user"] = [ "id" => 3, "name" => "rahul", "email" => "rahul@gmail.com" ]
    $_SESSION["success_message"] = "Listing deleted successfully"
    */
}