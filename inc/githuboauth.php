<?php

    /**
     * Wrapper for OAuth authenticated interaction with the Github API v3
     * 
     * See http://developer.github.com/v3/oauth/#web-application-flow for the authentication flow implemented here.
     * Make sure this script is required at the top of the .php page linked in your application's Callback URL.
     */
    class GithubOAuth
    {
        const BASE_URL = 'https://api.github.com';
        const AUTHORIZE_URL = 'https://github.com/login/oauth/authorize';
        const ACCESS_TOKEN_URL = 'https://github.com/login/oauth/access_token';

        const CLIENT_ID_SESSION_KEY = 'githuboauth.client_id';
        const CLIENT_SECRET_SESSION_KEY = 'githuboauth.client_secret';
        const ACCESS_CODE_SESSION_KEY = 'githuboauth.access_code';
        const ACCESS_CODE_REDEEMED_SESSION_KEY = 'githuboauth.access_code_redeemed';
        const ACCESS_TOKEN_SESSION_KEY = 'githuboauth.access_token';

        /**
         * Github API v3 Web Application Flow - Step 1: "Redirect users to request GitHub access"
         *
         * @param array $scope A string array of scopes of permission. (see http://developer.github.com/v3/oauth/#scopes)
         * @param string $client_id An optional string Github Application Client ID.
         * @param string $client_id An optional string Github Application Client Secret.
         *
         * Takes 'scope' of requested permissions (see http://developer.github.com/v3/oauth/#scopes) along with an optional Github 
         * Application's 'client_id' and 'client_secret' (see https://github.com/settings/applications to view/create applications),
         * and redirects the user to a Github page prompting the user to approve the application permissions requested in 'scope'. 
         * Upon authorizing the Application, the user will be redirected back to your application's callback URL (this page).  You 
         * must pass in $client_id and $client_secret if you haven't set them yet using set_client_id() and set_client_secret().
         */
        public function request_access_code($scope, $client_id = '', $client_secret = '') {
            if($client_id == '') {
                if(is_null($this->get_client_id())) {
                    throw new Exception("'client_id' is not yet set, and parameter 'client_id' was empty. See http://developer.github.com/v3/oauth/#web-application-flow");         
                }
            } else {
                $this->set_client_id($client_id);
            }
            if($client_secret == '') {
                if(is_null($this->get_client_secret())) {
                    throw new Exception("'client_secret' is not yet set, and parameter 'client_secret' was empty. See http://developer.github.com/v3/oauth/#web-application-flow");
                }
            } else {
                $this->set_client_secret($client_secret);
            }

            if(!isset($scope)) {
                throw new Exception("Parameter 'scope' was empty. See http://developer.github.com/v3/oauth/#scopes");
            } else if(!is_array($scope)) {
                throw new Exception("Parameter 'scope' should be an Array. See http://developer.github.com/v3/oauth/#scopes");
            }

            $url = HTTP::build_url(GithubOAuth::AUTHORIZE_URL, array(
                'client_id' => $this->get_client_id(),
                'scope' => implode(',', $scope)
            ));
            header("Location: $url");
        }

        /**
         * Github API v3 Web Application Flow - Step 2: "GitHub redirects back to your site"
         *
         * @param string $access_code The optional 'code' provided as a url parameter when Github redirects following authorization.      
         *
         * When the user authorizes the application following a call to 'request_access_code()', Github redirects back to the
         * current page passing a 'code' url parameter.  Pass this in as 'access_code' to acquire an 'access_token' which can then be used
         * to access all endpoints outlined in 'scope'. You must pass in 'access_code' if you haven't set it yet using 'set_access_code()'
         */
        public function request_access_token($access_code = '') {
            if(!is_null($this->get_access_token()) && $this->get_access_code_redeemed() == 1) {
                return;
            }
            if($access_code == '') {
                if(is_null($this->get_access_code())) {
                    throw new Exception("'access_code' is not yet set, and parameter 'code' was empty. See http://developer.github.com/v3/oauth/#web-application-flow");         
                }
            } else {
                $this->set_access_code($access_code);
                $this->set_access_code_redeemed(0);
            }
            if(is_null($this->get_client_id()) || is_null($this->get_client_secret())) {
                throw new Exception("Doesn't look like you've set 'client_id' and 'client_secret' or called 'request_access_code()' yet.");
            }
            if($this->get_access_code_redeemed()) {
                throw new Exception("The current 'access_code' was already redeemed.");
            }
            $response = HTTP::web_request('POST', GithubOAuth::ACCESS_TOKEN_URL, array(
                'client_id'    => $this->get_client_id(),
                'client_secret'=> $this->get_client_secret(),
                'code'         => $this->get_access_code()
            ));
            $to_replace = array('access_token=', '&token_type=bearer');
            $access_token = str_replace($to_replace, array('', ''), $response);
            if($access_token == 'error=bad_verification_code') {
                header("Location: " . $_SERVER['SCRIPT_NAME']);
            }
            $this->set_access_token($access_token);
            $this->set_access_code_redeemed(1);
        }

        /**
         * Issues an API request.
         * 
         * @param string $http_verb HEAD, GET, POST, PATCH, PUT, DELETE
         * @param string $endpoint The API endpoint's URL.
         * @param array[string]string $params The optional URL params OR data to post as JSON, depending on the endpoint.
         * @return string JSON containing [headers] and [response] received from Github.
         *
         * Takes an HTTP Verb (see http://developer.github.com/v3/#http-verbs), an endpoint (see http://developer.github.com/v3), and an 
         * optional assoc array of parameters.
         */
        public function api($http_verb, $endpoint, array $params = array()) {
            $http_verb = strtoupper($http_verb);
            $url = GithubOAuth::BASE_URL;
            $url .= ($endpoint[0] == '/') ? "{$endpoint}" : "/{$endpoint}";

            if($http_verb == 'POST' || $http_verb == 'PATCH' || $http_verb == 'PUT' || $http_verb == 'DELETE') {
                $url .= '?access_token=' . $this->get_access_token();
                return json_decode(HTTP::web_request($http_verb, $url, $params));
            } else {
             /*   if(empty($params['access_token']))
                    $params['access_token'] = $this->get_access_token();*/
                return json_decode(HTTP::web_request($http_verb, $url, $params));
            }
        }

        /**
         * Resets the current 'client_id', 'client_secret', 'access_code', and 'access_token'.
         */
        public function reset() {
            unset($_SESSION[GithubOAuth::CLIENT_ID_SESSION_KEY]);
            unset($_SESSION[GithubOAuth::CLIENT_SECRET_SESSION_KEY]);
            unset($_SESSION[GithubOAuth::ACCESS_CODE_SESSION_KEY]);
            unset($_SESSION[GithubOAuth::ACCESS_CODE_REDEEMED_SESSION_KEY]);
            unset($_SESSION[GithubOAuth::ACCESS_TOKEN_SESSION_KEY]);
        }


        /**
         * @return string Gets the current Github Application's 'client_id'.
         */
        public function get_client_id() {
            return $_SESSION[GithubOAuth::CLIENT_ID_SESSION_KEY];
        }
        /**
         * @param string $client_id Sets the current Github Application's 'client_id'.
         */
        public function set_client_id($client_id) {
            $_SESSION[GithubOAuth::CLIENT_ID_SESSION_KEY] = trim($client_id);
        }

        /**
         * @return string Gets the current Github Application's 'client_secret'.
         */
        public function get_client_secret() {
            return $_SESSION[GithubOAuth::CLIENT_SECRET_SESSION_KEY];
        }
        /**
         * @param string $client_secret Sets the current Github Application's 'client_secret'.
         */
        public function set_client_secret($client_secret) {
            $_SESSION[GithubOAuth::CLIENT_SECRET_SESSION_KEY] = trim($client_secret);
        }

        /**
         * @return string Gets the 'code' appended by Github to the callback URL in Step 2 of "Web Application Flow".
         * (see http://developer.github.com/v3/oauth/#web-application-flow)
         */
        public function get_access_code() {
            return $_SESSION[GithubOAuth::ACCESS_CODE_SESSION_KEY];
        }
        /**
         * @param string $access_code Sets the current Github Application's 'access_code'.
         */
        public function set_access_code($access_code) {
            if($access_code == $this->get_access_code()) {
                return;
            }
            $_SESSION[GithubOAuth::ACCESS_CODE_SESSION_KEY] = trim($access_code);
            $this->set_access_code_redeemed(0);
        }
        /**
         * @return int Returns 0 if 'access_code' is not redeemed, and 1 if so.
         */
        public function get_access_code_redeemed() {
            return $_SESSION[GithubOAuth::ACCESS_CODE_REDEEMED_SESSION_KEY];
        }
        /**
         * @param int $is_redeemed Set to 0 if not redeemed and 1 if redeemed.
         */
        public function set_access_code_redeemed($is_redeemed) {
            $_SESSION[GithubOAuth::ACCESS_CODE_REDEEMED_SESSION_KEY] = $is_redeemed;
        }

        /**
         * @return string Gets the 'access_token' used for authenticated API requests.
         */
        public function get_access_token() {
            return $_SESSION[GithubOAuth::ACCESS_TOKEN_SESSION_KEY];
        }
        /**
         * @param string $access_token Sets the 'access_token' for use in authenticating API requests.
         */
        public function set_access_token($access_token) {
            $_SESSION[GithubOAuth::ACCESS_TOKEN_SESSION_KEY] = trim($access_token);
        }
    }

    class HTTP_VERBS
    {
        const HEAD = 'HEAD';
        const GET = 'GET';
        const POST = 'POST';
        const PATCH = 'PATCH';
        const PUT = 'PUT';
        const DELETE = 'DELETE';
    }

    class HTTP
    {
        public static function web_request($http_verb, $url, array $params = array()) {
            $c = curl_init();
            switch(strtoupper($http_verb)) {
                case HTTP_VERBS::HEAD:
                    $response = HTTP::http_HEAD($c, $url, $params);
                    break;
                case HTTP_VERBS::GET;
                    $response = HTTP::http_GET($c, $url, $params);
                    break;
                case HTTP_VERBS::POST:
                    $response = HTTP::http_POST($c, $url, $params);
                    break;
                case HTTP_VERBS::PATCH:
                    $response = HTTP::http_PATCH($c, $url, $params);
                    break;
                case HTTP_VERBS::PUT:
                    $response = HTTP::http_PUT($c, $url, $params);
                    break;
                case HTTP_VERBS::DELETE:
                    $response = HTTP::http_DELETE($c, $url, $params);
                    break;
                default:
                    throw new Exception("Parameter 'http_verb' invalid. Valid options found in GithubOauth::HTTP_VERBS.");
            }
            curl_close($c);
            return $response;
        }

        // Can be issued against any resource to get just the HTTP header info.
        private static function http_HEAD($c, $url, $urlParams) {
            curl_setopt($c, CURLOPT_URL, $url);
            curl_setopt($c, CURLOPT_HEADER, 1);
            curl_setopt($c, CURLOPT_NOBODY, 1);
            curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
            curl_exec($c);
            $headers = curl_getinfo($c);
            return json_encode($headers);
        }

        // Used for retrieving resources.
        private static function  http_GET($c, $url, $urlParams) {
            $request = HTTP::build_url($url, $urlParams);
            curl_setopt($c, CURLOPT_URL, $request);
            curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
            $response = json_decode(curl_exec($c));
            $headers = curl_getinfo($c);
            return json_encode(compact('headers','response'));
        }

        // Used for creating resources, or performing custom actions (such as merging a pull request).
        private static function  http_POST($c, $url, $urlParams) {
            $requestingToken = $url == GithubOAuth::ACCESS_TOKEN_URL;
            curl_setopt($c, CURLOPT_URL, $url);
            curl_setopt($c, CURLOPT_POST, 1);
            curl_setopt($c, CURLOPT_RETURNTRANSFER, false);
            curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($c, CURLOPT_POSTFIELDS, $requestingToken ? $urlParams : json_encode($urlParams));
            curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
            $response = $requestingToken ? curl_exec($c) : json_decode(curl_exec($c));
            $headers = curl_getinfo($c);
            return $requestingToken ? $response : json_encode(compact('headers', 'response'));
        }

        // Used for updating resources with partial JSON data. For instance, an Issue resource has title and
        // body attributes. A PATCH request may accept one or more of the attributes to update the resource.
        // PATCH is a relatively new and uncommon HTTP verb, so resource endpoints also accept POST requests.
        private static function  http_PATCH($c, $url, $urlParams) {
            curl_setopt($c, CURLOPT_URL, $url);
            curl_setopt($c, CURLOPT_HEADER, false);
            curl_setopt($c, CURLOPT_CUSTOMREQUEST, 'PATCH');
            curl_setopt($c, CURLOPT_POSTFIELDS, json_encode($urlParams));
            curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
            $response = json_decode(curl_exec($c));
            $headers = curl_getinfo($c);
            return json_encode(compact('headers', 'response'));
        }

        // Used for replacing resources or collections.
        private static function  http_PUT($c, $url, $urlParams) {
            $c = curl_init();
            $putString = stripslashes(json_encode($urlParams));
            $data = tmpfile();
            fwrite($data, $putString);
            fseek($data, 0);
            curl_setopt($c, CURLOPT_URL, $url);
            curl_setopt($c, CURLOPT_PUT, true);
            curl_setopt($c, CURLOPT_INFILE, $data);
            curl_setopt($c, CURLOPT_BINARYTRANSFER, true);
            curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($c, CURLOPT_INFILESIZE, strlen($putString));
            curl_exec($c);
            $headers = curl_getinfo($c);
            return json_encode($headers);
        }

        // Used for deleting resources.
        private static function  http_DELETE($c, $url, $urlParams) {
            curl_setopt($c, CURLOPT_URL, $url);
            curl_setopt($c, CURLOPT_POSTFIELDS, json_encode($urlParams));
            curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($c, CURLOPT_HEADER, 0);
            curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($c, CURLOPT_CUSTOMREQUEST, 'DELETE');
            $response = json_decode(curl_exec($c));
            $headers = curl_getinfo($c);
            return json_encode(compact('headers', 'response'));
        }

        public static function build_url($url, $urlParams) {
            $request = $url;
            if(empty($urlParams)) return $request;
            foreach($urlParams as $k => $v) {
                $request .= (strstr($request, '?')) ? '&' : '?';
                $request .= ($k . '=' . $v);
            }
            return $request;
        }
    }
    
?>
