<?php

class Registration extends Endpoint {
    
    public function __construct() {
        $db = new Database("DB/development.sqlite");
        $this->validateRequestMethod("POST");
        $this->initialiseSQL();
        $queryResult = $db->executeSQL($this->getSQL(), $this->getSQLParams());

        $data = $_POST;

        $this->setData( array(
            "length" => 0, 
            "message" => "success",
            "data" => $data
          ));

    }

    protected function initialiseSQL() {
        $currentDateTime = date('d-m-Y H:i:s');
        $sql = "INSERT INTO users (username, email, password, createdAt) VALUES (:username, :email, :password, :createdAt)";
        $this->setSQL($sql);
        $params = [];
    
        if (isset($_POST['username'])) {
            $username = filter_var($_POST['username'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            if (strlen($username) > 3) {
                $params[':username'] = $username;
            } 
            else {
                throw new ClientErrorException("invalid username", 400);
            }
        } 
        else {
            throw new ClientErrorException("username is required", 400);
        }

        if (isset($_POST['email'])) {
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $params[':email'] = $email;
            } 
            else {
            throw new ClientErrorException("invalid email", 400);
            }
        } 
        else {
            throw new ClientErrorException("email is required", 400);
        }

        if (isset($_POST['password'])) {
            $password = $_POST['password'];
            if (strlen($password) >= 8) {
                $hashedpassword = password_hash($password, PASSWORD_DEFAULT);
                $params[':password'] = $hashedpassword;
            } 
            else {
                throw new ClientErrorException("invalid password", 400);
            }
        } 
        else {
            throw new ClientErrorException("password is required", 400);
        }
        
        $params[':createdAt'] = $currentDateTime;
        
        $this->setSQL($sql);
        $this->setSQLParams($params);
    }

    private function validateRequestMethod($method) {
        if ($_SERVER['REQUEST_METHOD'] != $method){
            throw new ClientErrorException("invalid request method", 405);
        }
    }
}

?>