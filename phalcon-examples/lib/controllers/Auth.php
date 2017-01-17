<?php 

namespace Lib\Controllers;

class Auth {

    private $_fbSecret = "";
    private $_googleSecret = "";
    private $_twitterSecret = "";
    private $_twitterKey = "";

    /*
     * Facebook Login
     */
    public function facebook($app) {   
        $client = new \GuzzleHttp\Client();

        //base data
        $request = $app->request->getJsonRawBody();

        $params = [
            'code' => $request->code,
            'client_id' => $request->clientId,
            'redirect_uri' => $request->redirectUri,
            'client_secret' => $this->_fbSecret
        ];

        // Step 1. Exchange authorization code for access token.
        $accessTokenResponse = $client->request('GET', 'https://graph.facebook.com/v2.5/oauth/access_token', [
            'query' => $params
        ]);
        $accessToken = json_decode($accessTokenResponse->getBody(), true);

        // Step 2. Retrieve profile information about the current user.
        $fields = 'id,email,first_name,last_name,link,name';
        $profileResponse = $client->request('GET', 'https://graph.facebook.com/v2.5/me', [
            'query' => [
                'access_token' => $accessToken['access_token'],
                'fields' => $fields
            ]
        ]);
        $profile = json_decode($profileResponse->getBody(), true);

        //check email first
        $udb = new \Lib\Models\Users();
        //saving profile with email linked
        $phql = "SELECT id 
                 FROM Lib\Models\Users 
                 WHERE 
                    email = :email: AND (facebook != :facebook: OR facebook IS NULL)";
        $checkemail = $app->modelsManager->executeQuery($phql, array(
            'email' => $profile['email'],
            'facebook' => $profile['id'],
        ));
        
        if(count($checkemail) > 0) {
            foreach($checkemail as $ce) {
                $idlinked = $ce->id;
            }
            //saving profile with email linked
            $phql = "UPDATE Lib\Models\Users SET facebook = :fbprofileid: WHERE id = :id:";
            $status = $app->modelsManager->executeQuery($phql, array(
                'id' => $idlinked,
                'fbprofileid' => $profile['id'],
            ));
        }

        //check if a linked account already exists
        $check = $udb->find("facebook = '" . $profile['id'] . "'");
        $linkeduserid = NULL;
        if(count($check) > 0) {
            //account exists
            $linkeduserid = 1;
        }

        //is user logged in?
        $user = new \Lib\Libraries\User();
        if($id = $user->isLoggedIn()) {
            //user is logged in
            if($linkeduserid == NULL) {
                //linking fb profile
                $phql = "UPDATE Lib\Models\Users SET facebook = :fbprofileid: WHERE id = :id:";
                $status = $app->modelsManager->executeQuery($phql, array(
                    'id' => $id,
                    'fbprofileid' => $profile['id'],
                ));

                $return = 1;
            } else {
                $return = 2;
            }
        } else {
            //user not logged in
            if($linkeduserid == NULL) {
                //create a random password
                $salt = $user->createSalt();
                $password = $user->hashPassword($user->randomPassword(), $salt);
                //register the new account
                $udb->nome     = $profile['name'];
                $udb->email    = $profile['email'];
                $udb->password = $password;
                $udb->facebook = $profile['id'];
                $udb->banned   = FALSE;
                $udb->salt     = $salt;
                $udb->create();

                $myuser = $udb->findFirst("facebook = '" . $profile['id'] . "'");
                $id = $myuser->id;

                $return = 3;
            } else {
                //login into the account
                $myuser = $udb->findFirst("facebook = '" . $profile['id'] . "'");
                $id = $myuser->id;

                $return = 4;
            }
        }

        //key
        $key = new \Lib\Libraries\Key();

        //message system
        $message = new \Lib\Libraries\Message();

        //sending answer to the system
        switch($return) {
            case 1:
                $data = array(
                    "uid" => $id
                );
                $key->create($data);
                $message->addToken($key->getKey());
                $app->response->setContentType('application/json')->sendHeaders();
                $message->getMessage(TRUE);
                break;
            case 2:
                $message->addError("fb_already_linked", 401)->majorErrorReport();
                $message->getMessage(TRUE);
                break;
            case 3:
                $data = array(
                    "uid" => $id
                );
                $key->create($data);
                $message->addToken($key->getKey());
                $app->response->setContentType('application/json')->sendHeaders();
                $message->getMessage(TRUE);
                break;
            case 4:
                $data = array(
                    "uid" => $id
                );
                $key->create($data);
                $message->addToken($key->getKey());
                $app->response->setContentType('application/json')->sendHeaders();
                $message->getMessage(TRUE);
                break;
        }
    }

    /* 
     * Google Login
     */
    public function google($app)
    {
        $client = new \GuzzleHttp\Client();

        //base data
        $request = $app->request->getJsonRawBody();

        $params = [
            'code' => $request->code,
            'client_id' => $request->clientId,
            'redirect_uri' => $request->redirectUri,
            'client_secret' => $this->_googleSecret,
            'grant_type' => 'authorization_code',
        ];

        // Step 1. Exchange authorization code for access token.
        $accessTokenResponse = $client->request('POST', 'https://accounts.google.com/o/oauth2/token', [
            'form_params' => $params
        ]);
        $accessToken = json_decode($accessTokenResponse->getBody(), true);

        // Step 2. Retrieve profile information about the current user.
        $profileResponse = $client->request('GET', 'https://www.googleapis.com/plus/v1/people/me/openIdConnect', [
            'headers' => array('Authorization' => 'Bearer ' . $accessToken['access_token'])
        ]);
        $profile = json_decode($profileResponse->getBody(), true);

        //check email first
        $udb = new \Lib\Models\Users();
        //saving profile with email linked
        $phql = "SELECT id 
                 FROM Lib\Models\Users 
                 WHERE 
                    email = :email: AND (google != :googleid:
                    OR google IS NULL)";
        $checkemail = $app->modelsManager->executeQuery($phql, array(
            'email' => $profile['email'],
            'googleid' => $profile['sub'],
        ));
        
        if(count($checkemail) > 0) {
            foreach($checkemail as $ce) {
                $idlinked = $ce->id;
            }
            //saving profile with email linked
            $phql = "UPDATE Lib\Models\Users SET google = :googleid: WHERE id = :id:";
            $status = $app->modelsManager->executeQuery($phql, array(
                'id' => $idlinked,
                'googleid' => $profile['sub'],
            ));
        }

        //check if a linked account already exists
        $check = $udb->find("google = '" . $profile['sub'] . "'");
        $linkeduserid = NULL;
        if(count($check) > 0) {
            //account exists
            $linkeduserid = 1;
        }

        //is user logged in?
        $user = new \Lib\Libraries\User();
        if($id = $user->isLoggedIn()) {
            //user is logged in
            if($linkeduserid == NULL) {
                //linking fb profile
                $phql = "UPDATE Lib\Models\Users SET google = :googleid: WHERE id = :id:";
                $status = $app->modelsManager->executeQuery($phql, array(
                    'id' => $id,
                    'googleid' => $profile['sub'],
                ));

                $return = 1;
            } else {
                $return = 2;
            }
        } else {
            //user not logged in
            if($linkeduserid == NULL) {
                //create a random password
                $salt = $user->createSalt();
                $password = $user->hashPassword($user->randomPassword(), $salt);
                //register the new account
                $udb->nome     = $profile['name'];
                $udb->email    = $profile['email'];
                $udb->password = $password;
                $udb->google = $profile['sub'];
                $udb->banned   = FALSE;
                $udb->salt     = $salt;
                $udb->create();

                $myuser = $udb->findFirst("google = '" . $profile['sub'] . "'");
                $id = $myuser->id;

                $return = 3;
            } else {
                //login into the account
                $myuser = $udb->findFirst("google = '" . $profile['sub'] . "'");
                $id = $myuser->id;

                $return = 4;
            }
        }

        //key
        $key = new \Lib\Libraries\Key();

        //message system
        $message = new \Lib\Libraries\Message();

        //sending answer to the system
        switch($return) {
            case 1:
                $data = array(
                    "uid" => $id
                );
                $key->create($data);
                $message->addToken($key->getKey());
                $app->response->setContentType('application/json')->sendHeaders();
                $message->getMessage(TRUE);
                break;
            case 2:
                $message->addError("google_already_linked", 401)->majorErrorReport();
                $message->getMessage(TRUE);
                break;
            case 3:
                $data = array(
                    "uid" => $id
                );
                $key->create($data);
                $message->addToken($key->getKey());
                $app->response->setContentType('application/json')->sendHeaders();
                $message->getMessage(TRUE);
                break;
            case 4:
                $data = array(
                    "uid" => $id
                );
                $key->create($data);
                $message->addToken($key->getKey());
                $app->response->setContentType('application/json')->sendHeaders();
                $message->getMessage(TRUE);
                break;
        }
    }

        /**
     * Login with Twitter.
     */
    public function twitter($app)
    {
        //base data
        $request = $app->request->getJsonRawBody();

        $stack = \GuzzleHttp\HandlerStack::create();
        // Part 1 of 2: Initial request from Satellizer.
        if (!property_exists($request, 'oauth_token') || !property_exists($request, 'oauth_verifier'))
        {
            $stack = \GuzzleHttp\HandlerStack::create();
            $requestTokenOauth = new \GuzzleHttp\Subscriber\Oauth\Oauth1([
              'consumer_key' => $this->_twitterKey,
              'consumer_secret' => $this->_twitterSecret,
              'callback' => $request->redirectUri,
              'token' => '',
              'token_secret' => ''
            ]);
            $stack->push($requestTokenOauth);
            $client = new \GuzzleHttp\Client([
                'handler' => $stack
            ]);
            // Step 1. Obtain request token for the authorization popup.
            $requestTokenResponse = $client->request('POST', 'https://api.twitter.com/oauth/request_token', [
                'auth' => 'oauth'
            ]);
            $oauthToken = array();
            parse_str($requestTokenResponse->getBody(), $oauthToken);
            // Step 2. Send OAuth token back to open the authorization screen.
            header('Content-Type: application/json');
            echo json_encode($oauthToken);
        }
        // Part 2 of 2: Second request after Authorize app is clicked.
        else
        {
            $accessTokenOauth = new \GuzzleHttp\Subscriber\Oauth\Oauth1([
                'consumer_key' => $this->_twitterKey,
                'consumer_secret' => $this->_twitterSecret,
                'token' => $request->oauth_token,
                'verifier' => $request->oauth_verifier,
                'token_secret' => ''
            ]);
            $stack->push($accessTokenOauth);
            $client = new \GuzzleHttp\Client([
                'handler' => $stack
            ]);
            // Step 3. Exchange oauth token and oauth verifier for access token.
            $accessTokenResponse = $client->request('POST', 'https://api.twitter.com/oauth/access_token', [
                'auth' => 'oauth'
            ]);

            $accessToken = array();
            parse_str($accessTokenResponse->getBody(), $accessToken);
            $profileOauth = new \GuzzleHttp\Subscriber\Oauth\Oauth1([
                'consumer_key' => $this->_twitterKey,
                'consumer_secret' => $this->_twitterSecret,
                'oauth_token' => $accessToken['oauth_token'],
                'token_secret' => ''
            ]);

            $stack->push($profileOauth);
            $client = new \GuzzleHttp\Client([
                'handler' => $stack
            ]);
            // Step 4. Retrieve profile information about the current user.
            $twitter = new \Abraham\TwitterOAuth\TwitterOAuth($this->_twitterKey, $this->_twitterSecret, $accessToken['oauth_token'], $accessToken['oauth_token_secret']);
            $profile = $twitter->get("account/verify_credentials", ["include_email" => "true"]);
            //check email first
            $udb = new \Lib\Models\Users();
            //saving profile with email linked
            $phql = "SELECT id 
                     FROM Lib\Models\Users 
                     WHERE 
                        email = :email: AND (twitter != :twitterid: OR twitter IS NULL)";
            $checkemail = $app->modelsManager->executeQuery($phql, array(
                'email' => $profile->email,
                'twitterid' => $profile->id,
            ));
            
            if(count($checkemail) > 0) {
                foreach($checkemail as $ce) {
                    $idlinked = $ce->id;
                }
                //saving profile with email linked
                $phql = "UPDATE Lib\Models\Users SET twitter = :twitterid: WHERE id = :id:";
                $status = $app->modelsManager->executeQuery($phql, array(
                    'id' => $idlinked,
                    'twitterid' => $profile->id,
                ));
            }

            //check if a linked account already exists
            $check = $udb->find("twitter = '" . $profile->id . "'");
            $linkeduserid = NULL;
            if(count($check) > 0) {
                //account exists
                $linkeduserid = 1;
            }

            //is user logged in?
            $user = new \Lib\Libraries\User();
            if($id = $user->isLoggedIn()) {
                //user is logged in
                if($linkeduserid == NULL) {
                    //linking fb profile
                    $phql = "UPDATE Lib\Models\Users SET twitter = :twitterid: WHERE id = :id:";
                    $status = $app->modelsManager->executeQuery($phql, array(
                        'id' => $id,
                        'twitterid' => $profile->id,
                    ));

                    $return = 1;
                } else {
                    $return = 2;
                }
            } else {
                //user not logged in
                if($linkeduserid == NULL) {
                    //create a random password
                    $salt = $user->createSalt();
                    $password = $user->hashPassword($user->randomPassword(), $salt);
                    //register the new account
                    $udb->nome     = $profile->name;
                    $udb->email    = $profile->email;
                    $udb->password = $password;
                    $udb->twitter  = $profile->id;
                    $udb->banned   = FALSE;
                    $udb->salt     = $salt;
                    $udb->create();

                    $myuser = $udb->findFirst("twitter = '" . $profile->id . "'");
                    $id = $myuser->id;

                    $return = 3;
                } else {
                    //login into the account
                    $myuser = $udb->findFirst("twitter = '" . $profile->id . "'");
                    $id = $myuser->id;

                    $return = 4;
                }
            }

            //key
            $key = new \Lib\Libraries\Key();

            //message system
            $message = new \Lib\Libraries\Message();

            //sending answer to the system
            switch($return) {
                case 1:
                    $data = array(
                        "uid" => $id
                    );
                    $key->create($data);
                    $message->addToken($key->getKey());
                    $app->response->setContentType('application/json')->sendHeaders();
                    $message->getMessage(TRUE);
                    break;
                case 2:
                    $message->addError("twitter_already_linked", 401)->majorErrorReport();
                    $message->getMessage(TRUE);
                    break;
                case 3:
                    $data = array(
                        "uid" => $id
                    );
                    $key->create($data);
                    $message->addToken($key->getKey());
                    $app->response->setContentType('application/json')->sendHeaders();
                    $message->getMessage(TRUE);
                    break;
                case 4:
                    $data = array(
                        "uid" => $id
                    );
                    $key->create($data);
                    $message->addToken($key->getKey());
                    $app->response->setContentType('application/json')->sendHeaders();
                    $message->getMessage(TRUE);
                    break;
            }
        }
    }
}