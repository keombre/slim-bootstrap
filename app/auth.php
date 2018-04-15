<?php

class auth {
    
    protected $container;
    protected $db;
    public $user;
    
    function __construct(\Slim\Container $container) {
        $this->container = $container;
        $this->db = $this->container->db;

        if ($this->logged()) {
            $this->user = $this->db->get("users", "*", ["token" => $_SESSION['token']]);
        }
    }

    function login($name, $pass) {
        if ($hash = $this->db->get("users", "pass", ["name" => $name])) {
            if (password_verify($pass, $hash)) {
                do {
                    $token = bin2hex(\openssl_random_pseudo_bytes(4));
                } while ($this->db->has("users", ["token" => $token]));
                
                $_SESSION['token'] = $token;
                $this->db->update("users", ["token" => $token], ["name" => $name]);
                return true;
            }
            return false;
        }
        return false;
    }

    function logout() {
        if (!$this->logged())
            return false;
        
        $this->db->update("users", ["token" => null], ["token" => $_SESSION['token']]);
        unset($_SESSION['token']);
        return true;
    }

    function register($name, $pass) {
        if ($this->db->has("users", ["name" => $name]))
            return false;
        
        if (strlen($name) > 20 || strlen($pass) > 20)
            return false;
        
        if (preg_match('/[^\x20-\x7f]/', $name))
            return false;
        
        $hash = \password_hash($pass, \PASSWORD_DEFAULT);
        $this->db->insert("users", ["name" => $name, "pass" => $hash]);
        return true;
    }

    function logged() {
        if ($this->hasToken() && $this->db->has("users", ["token" => $_SESSION['token']]))
            return true;
        return false;
    }

    function hasToken() {
        if (is_string(@$_SESSION['token']) && strlen(@$_SESSION['token']) == 8)
            return true;
        return false;
    }

    function changePass($pass) {
        if (!$this->logged())
            return false;
        
        $this->db->update("users", ["pass" => password_hash($pass, PASSWORD_DEFAULT)], ["token" => $_SESSION['token']]);
        return true;
    }

    function checkPassword($pass) {
        if (!$this->logged())
            return false;
        
        return password_verify($pass, $this->db->get("users", "pass", ["token" => $_SESSION['token']]));
    }
}
