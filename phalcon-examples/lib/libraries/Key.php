<?php

namespace Lib\Libraries;

use \Firebase\JWT\JWT;

Class Key {
    private $_secret = "19d74kUR7430Sau2n484HD"; //secret key
    private $_iss = "http://example.com"; //issuer, who gives the jwt (us)
    private $_nbf = 5; //not valid before
    private $_exp = 3600; //expire in
    private $_enc = "HS512"; // Algorithm used to sign the token, see https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40#section-3
    private $_generatedKey;

    /*
     * Returning the whole key
     */
    public function getKey() {
        return $this->_generatedKey;
    }

    /*
     * Returning secret key
     */
    public function secret() {
        return $this->_secret();
    }

    /*
     * Returning encryption method
     */

    public function enc() {
        return $this->_enc;
    }

    /*
     * Checking if the token is valid or not, returns decoded Token;
     */
    public function check() {
        $myheader = apache_request_headers(); //getting headers
        $bearer = explode(" ", $myheader['Authorization'])[1]; //exploding "Bearer {token}"
        JWT::$leeway = 60; // $leeway in seconds
        try{
            $decoded = JWT::decode($bearer, $this->_secret, array($this->_enc));

            return $decoded;
        } catch(Exception $e) {
            \Lib\Libraries\Message::addError($e->message, 401)->majorErrorReport();
        }
    }

    /*
     * Creating token from data array() $data and saving into $_generatedKey
     */
    public function create($data) {
        $array = array(
            "iss" => $this->_iss,
            "iat" => time(),
            "nbf" => time() + $this->_nbf,
            "exp" => time() + $this->_exp,
            "data" => $data
        );

        try {
            //creating encoded token
            $jwt = JWT::encode(
                $data,      
                $this->_secret, 
                $this->_enc     
            );
            //save in lib
            $this->_generatedKey = $jwt;
        } catch(Exception $e) {
            \Lib\Libraries\Message::addError($e->message, 401)->majorErrorReport();
        }
    }
}