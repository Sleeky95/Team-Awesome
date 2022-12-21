<?php
include_once '../model/User.php';
include_once '../utils/helpers.php';
include_once '../utils/constants.php';
include_once '../model/UserType.php';

class UserController
{

    private $user;

    function __construct($db)
    {
        $this->user = new User($db);
    }

    function register($user) {
        $this->user->firstName = $user->firstName;
        $this->user->surname = $user->surname;
        $this->user->email = $user->email;
        $this->user->type = strtoupper($user->type);
        $this->user->password = $user->password;
//        $this->user->profileImageUrl = $user->profileImageUrl;
        if ($this->user->type != UserType::student && $this->user->type != UserType::supervisor) {
            return error("Invalid user type ". $user->type);
        }
        $response = $this->user->addUser();
        if ($response[RESPONSE_STATUS] == DbResponse::STATUS_ERROR) {
            return error($response[RESPONSE_MESSAGE]);
        }
        return success("Registration successful", $response);
    }

    function login($email, $password) {
        $regUser = $this->user->getUserByEmail($email);
        if ($regUser[RESPONSE_STATUS] == DbResponse::STATUS_SUCCESS) {
            $user = $regUser[RESPONSE_DATA][0];
            if (!password_verify($password, $user['password'])) {
                return error("Password is invalid");
            }
            return success("Login successful", $this->transform($user));
        } else {
            return error("Email does not exist");
        }
    }

    function transform($content) {
        return array(
            'id' => $content['id'],
            'firstName'=> $content['first_name'],
            'surname' => $content['surname'],
            'email' => $content['email'],
            'type' => $content['type']
        );
    }
}