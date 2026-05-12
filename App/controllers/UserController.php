<?php
namespace App\Controllers;

use Framework\Database;
use Framework\Validation;
use Framework\Session;


class UserController
{
    protected $db;

    public function __construct()
    {
        $config = require basePath("config/db.php");
        $this->db = new Database($config);
    }

    /**
     * show register page
     * @return void
     */
    public function create(){
        loadView("users/create");
    }


    /**
     * show login page
     * @return void
     */
    public function login(){
        loadView("users/login");
    }


    /**
     * register a new user
     * @return void
     */
    public function store(){
        $name = $_POST["name"];
        $email = $_POST["email"];
        $city = $_POST["city"];
        $state = $_POST["state"];
        $password = $_POST["password"];
        $passwordConfirm = $_POST["password_confirmation"];
        // validation checks
        $errors = [];
        if( !Validation::email($email)){
            $errors["email"] = "Enter a valid email address";
        }
        if( !Validation::string($name, 3, 50)) {
            $errors["name"] = "Name must be between 3 and 50 characters";
        }
        if( !Validation::string($password, 6, 50)) {
            $errors["password"] = "Password must be minimum 6 characters";
        }
        if( !Validation::match($password, $passwordConfirm)) {
            $errors["password_confirmation"] = "Passwords do not match";
        }
        if( !empty($errors)){
            loadView("users/create", [
                "errors" => $errors,
                "user" => [
                    "name" => $name, 
                    "email" => $email, 
                    "city" => $city, 
                    "state" => $state 
                ]
            ]);
            exit;
        }
        // check if email already exists
        $params = [ 
            "email" => $email 
        ];
        $user = $this->db->query("SELECT * FROM users WHERE email = :email", $params)->fetch();
        if($user){
            $errors["email"] = "Email already exists";
            loadView("users/create", [
                "errors" => $errors,
                "user" => [ 
                    "name" => $name, 
                    "email" => $email, 
                    "city" => $city, 
                    "state" => $state 
                ]
            ]);
            exit;
        }
        // if everything fine ~
        $params = [
            "name" => $name,
            "email" => $email,
            "city" => $city,
            "state" => $state,
            "password" => password_hash($password, PASSWORD_DEFAULT)
        ];
        $this->db->query("
            INSERT INTO users (name, email, city, state, password)
            VALUES (:name, :email, :city, :state, :password)
        ", $params);

        $userId = (int)$this->db->conn->lastInsertId();   //get current generated id of the inserted user from db 
        Session::set("user", [
            "id" => $userId,
            "name" => $name,
            "email" => $email
        ]);

        redirect("/");
    }


    /**
     * login a user
     * @return void
     */
    public function authenticate(){
        $email = $_POST["email"];
        $password = $_POST["password"];
        $errors = [];
        if( !Validation::email($email)){
            $errors["email"] = "Please enter a valid email";
        }
        if ( !Validation::string($password, 6, 50)) {
            $errors["email"] = "Password must be minimum 6 characters";
        }
        if( !empty($errors)){
            loadView("users/login", [ 
                "errors" => $errors 
            ]);
            exit;
        }
        //check user credentials against db
        $params = [
            "email" => $email
        ];
        $user = $this->db->query("SELECT * FROM users WHERE email = :email", $params)->fetch();
        if( !$user){
            $errors["email"] = "Incorrect credentials";
            loadView("users/login", [
                "errors" => $errors
            ]);
            exit;
        }
        if( !password_verify($password, $user["password"])){
            $errors["password"] = "Incorrect credentials";
            loadView("users/login", [
                "errors" => $errors
            ]);
            exit;
        }
        //if credentials valid ~
        Session::set("user", [
            "id" => $user["id"],
            "name" => $user["name"],
            "email" => $user["email"]
        ]);

        redirect("/");
    }


    /**
     * logout user
     * @return void
     */
    public function logout(){
        Session::clearAll();
        $params = session_get_cookie_params();
        setcookie("PHPSESSID", "", time() - 86400, $params["path"], $params["domain"]);   // delete session cookie
        redirect("/");
    }
}