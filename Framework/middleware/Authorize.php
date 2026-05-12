<?php
namespace Framework\Middleware;

use Framework\Session;


class Authorize
{   
    /**
     * check if user logged in
     * @return bool
     */
    public function isAuthenticated(){
        return Session::has("user");
    }


    /**
     * handle user's request according to role
     * @param string $role
     * @return bool
     */
    public function handle($role){
        if($role === "guest"  &&  $this->isAuthenticated()){   //if page for guest and user already logged in
            return redirect("/");
        }
        else if($role === "auth"  &&  !$this->isAuthenticated()){   //if page for loggedin users and user not logged in
            return redirect("/auth/login");
        }
    }
}