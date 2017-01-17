<?php 

namespace Lib\Libraries;

class User {

    private $cost = 10;
    private $_randNumChars = 9;

    /*
     * Login function, returns user id on success, false on error
     */
    public function login($email, $password, $app) {
        //loading id by email
        $phql = "SELECT id, password, salt FROM \Lib\Models\Users WHERE email = :email: LIMIT 1";
        $results = $app->modelsManager->executeQuery($phql, array(
                    'email' => $email,
                ));

        if($results->count() == 1) {
            //saving vars
            $id = $results->getFirst()->id;
            $salt = $results->getFirst()->salt;
            $dbpassword = $results->getFirst()->password;
            
            //encoding password sent by user
            $upassword = $this->hashPassword($password, $salt);

            if($upassword == $dbpassword) {
                return $id;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    /*
     * Create a random password
     */
    public function randomPassword() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < $this->_randNumChars; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    /*
     * Return hashed password from password and salt
     */
    public function hashPassword($password, $salt) {
        $hash = crypt($password, $salt);

        return $hash;
    }

    /*
     * Create salt for password during registration
     */
    public function createSalt() {
        $salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
        $salt = sprintf("$2a$%02d$", $this->cost) . $salt;

        return $salt;
    }

    /*
     * Check if user is logged in
     * Returns false of not logged in, returns user data if is logged in
     */
    public function isLoggedIn() {
        //getting headers
        $myheader = apache_request_headers();

        //if I have Authorization header ('Bearer {Key}' is expected)
        if(array_key_exists('Authorization', $myheader)) {
            //load resources
            $keyLib = new \Lib\Libraries\Key();

            //get the key
            $decoded = $keyLib->check();

            return $decoded;
        } else {
            return FALSE;
        }
    }
}