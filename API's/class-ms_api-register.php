<?php
/**
 * Created by PhpStorm.
 * User: Mustafa Shaaban
 * Date: 6/20/2019
 * Time: 6:10 PM
 */

class Ms_api_register extends WP_REST_Request
{
    public function __construct()
    {
        add_action('rest_api_init', array($this, 'MS_API_register_new_user'));
    }
    public function MS_API_register_new_user($request){
        /**
         * Handle Register User request.
         */
        register_rest_route('wp/v2', 'users/register', array(
            'methods' => 'POST',
            'callback' => array($this, 'MS_API_user_endpoint_handler'),
        ));
    }

    public function MS_API_user_endpoint_handler($request = null) {
        $response = array();
        $parameters = $request->get_body_params();
        $username = sanitize_text_field($parameters['username']);
        $email = sanitize_text_field($parameters['email']);
        $password = sanitize_text_field($parameters['password']);

        $error = new WP_Error();
        if (empty($username)) {
            $error->add(400, __("Username field 'username' is required.", 'wp-rest-user'), array('status' => 400));
            return $error;
        }
        if (empty($email)) {
            $error->add(401, __("Email field 'email' is required.", 'wp-rest-user'), array('status' => 400));
            return $error;
        }
        if (empty($password)) {
            $error->add(404, __("Password field 'password' is required.", 'wp-rest-user'), array('status' => 400));
            return $error;
        }

        // if (empty($role)) {
        //  $role = 'subscriber';
        // } else {
        //     if ($GLOBALS['wp_roles']->is_role($role)) {
        //      // Silence is gold
        //     } else {
        //    $error->add(405, __("Role field 'role' is not a valid. Check your User Roles from Dashboard.", 'wp_rest_user'), array('status' => 400));
        //    return $error;
        //     }
        // }

        $user_id = username_exists($username);
        if (!$user_id && false == email_exists($email)) {
            $user_id = wp_create_user($username, $password, $email);
            if (!is_wp_error($user_id)) {
                // Ger User Meta Data (Sensitive, Password included. DO NOT pass to front end.)
                $user = get_user_by('id', $user_id);
                // $user->set_role($role);
                $user->set_role('subscriber');
                $response['code'] = 200;
                $response['message'] = __("User '" . $username . "' Registration was Successful", "wp-rest-user");
            } else {
                return $user_id;
            }
        } else {
            $error->add(406, __("Email already exists, please try 'Reset Password'", 'wp-rest-user'), array('status' => 400));
            return $error;
        }
        return new WP_REST_Response($response, 123);
    }
}
new Ms_api_register();