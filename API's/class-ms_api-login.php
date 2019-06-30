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
    public function MS_API_login_user($request){
        /**
         * Handle login User request.
         */
        register_rest_route('MSAPI', 'stars/login', array(
            'methods' => 'POST',
            'callback' => array($this, 'MS_API_user_endpoint_handler'),
        ));
    }
    public function MS_API_user_endpoint_handler($request = null) {
        $error = new WP_Error();
        $parameters = $request->get_body_params();
        $username = sanitize_text_field($parameters['user_login']);
        $password =  sanitize_text_field($parameters['user_password']);
        $response = array(
            'code' => 400,
            'message' => 'Something went wrong!',
        );

        if (empty($username)) {
            $error->add(400, "Username field 'username' is required.", array('status' => 400));
            return $error;
        }
        if (empty($password)) {
            $error->add(404, "Password field 'password' is required.", array('status' => 400));
            return $error;
        }

        $creds = array();
        $creds['user_login'] = $username;
        $creds['user_password'] =  $password;
        $user = wp_signon( $creds, false );

        if ( is_wp_error($user) ) {
            $response = [
                'code' => 400,
                'message' => 'Username or Password is invalid!'
            ];
        } else {
            $userdata = $this->MSAPI_get_userData($user);
            $response = [
                'code' => 200,
                'data' => $userdata,
                'message' => 'The user has been logged in successfully'
            ];
        }

        return new WP_REST_Response($response, 123);
    }
    public function MSAPI_get_userData($user)
    {
        $user_basics = $user->data;
        $user_meta = get_user_meta($user->ID);
        $object = new stdClass();
        foreach ($user_meta as $key => $value) {
            $object->$key = $value[0];
        }
        $data = (object)array_merge((array)$user_basics, (array)$object);
        return $data;
    }

}
new Ms_api_login();