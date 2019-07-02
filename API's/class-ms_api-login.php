<?php
/**
 * Created by PhpStorm.
 * User: Mustafa Shaaban
 * Date: 6/20/2019
 * Time: 6:10 PM
 */

class Ms_api_login extends WP_REST_Request
{
    public function __construct()
    {
        add_action('rest_api_init', array($this, 'MS_API_login_user'));
    }

    public function MS_API_login_user($request)
    {
        /**
         * Handle login User request.
         */
        register_rest_route('MSAPI', 'users/login', array(
            'methods'   => 'POST',
            'callback'  => array($this, 'MS_API_user_endpoint_handler'),
        ));
    }

    public function MS_API_user_endpoint_handler($request = null)
    {
        $error      = new WP_Error();
        $parameters = $request->get_body_params();
        $username   = sanitize_text_field($parameters['user_login']);
        $password   = sanitize_text_field($parameters['user_password']);

        $response = array(
            'code'      => 400,
            'message'   => 'Something went wrong!',
        );

        if (empty($username)) {
            $error->add(400, "Username field 'username' is required.", array('status' => 400));
            return $error;
        }
        if (empty($password)) {
            $error->add(404, "Password field 'password' is required.", array('status' => 400));
            return $error;
        }

        $user_login = get_user_by('login', $username);
        if(!$user_login) {
            $user_login = get_user_by('email', $username);
            if(!$user_login) {
                $user_query = new WP_User_Query( array( 'meta_key' => 'phone', 'meta_value' => $username ) );
                $user_meta = $user_query->get_results();

                if (empty($user_meta)) {
                    $response = [
                        'code'      => 400,
                        'message'   => 'Username or Password is invalid!'
                    ];
                    return new WP_REST_Response($response, 123);
                }
                if (count($user_meta) > 1) {
                    $response = [
                        'code'      => 400,
                        'message'   => 'Failed login with duplicate username'
                    ];
                    return new WP_REST_Response($response, 123);
                }
                if (!empty($user_meta) && count($user_meta) === 1) {
                    $user_login = $user_meta[0];
                    $username = $user_login->data->user_login;
                }

            }
        }

        $creds                  = array();
        $creds['user_login']    = $username;
        $creds['user_password'] = $password;
        $user                   = wp_signon($creds, false);

        if (is_wp_error($user)) {
            $response = [
                'code'      => 400,
                'message'   => 'Username or Password is invalid!'
            ];
        } else {
            $method     = 'POST';
            $url        = home_url() . "/wp-json/jwt-auth/v1/token";
            $data       = array('username' => $username,'password' => $password);
            $token      = $this->getToken($method, $url, $data);
            $userdata   = $this->MSAPI_get_userData($user);
            $response   = [
                'code'      => 200,
                'message'   => 'The user has been logged in successfully',
                'data'      => $userdata,
                'token'     => $token->token
            ];
        }
        return new WP_REST_Response($response, 123);
    }

    public function MSAPI_get_userData($user)
    {
        $user_basics    = $user->data;
        $user_meta      = get_user_meta($user->ID);
        $object         = new stdClass();
        foreach ($user_meta as $key => $value) {
            $object->$key = $value[0];
        }
        $data = (object)array_merge((array)$user_basics, (array)$object);
        return $data;
    }

    public function getToken($method, $url, $data = false)
    {
        if (!$data || empty($data)) {
            return false;
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => http_build_query($data),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return json_decode($response);
        }
    }

}

new Ms_api_login();