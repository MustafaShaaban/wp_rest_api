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
        register_rest_route('MSAPI', 'users/register', array(
            'methods' => 'POST',
            'callback' => array($this, 'MS_API_user_endpoint_handler'),
        ));
    }

    public function MS_API_user_endpoint_handler($request = null)
    {
        $response = array();
        $parameters = $request->get_body_params();
        $username = sanitize_text_field($parameters['username']);
        $email = sanitize_text_field($parameters['email']);
        $password = sanitize_text_field($parameters['password1']);
        $password2 = sanitize_text_field($parameters['password2']);
        $phone = sanitize_text_field($parameters['phone']);
        $occupation = sanitize_text_field($parameters['occupation']); // peepso_user_field_146


        $error = new WP_Error();
        if (empty($username)) {
            $error->add(400, "Username field 'username' is required.", array('status' => 400));
            return $error;
        }
        if (empty($email)) {
            $error->add(401,"Email field 'email' is required.", array('status' => 400));
            return $error;
        }
        if (empty($password)) {
            $error->add(402, "Password field 'password' is required.", array('status' => 400));
            return $error;
        }
        if (empty($phone)) {
            $error->add(403, "Phone number field is required.", array('status' => 400));
            return $error;
        }
        if (empty($occupation)) {
            $error->add(404, "occupation field is required.", array('status' => 400));
            return $error;
        }


        if ( ! validate_username( $username ) ) {
            $error->add( 405,'Sorry, the username you entered is not valid', array('status' => 400) );
            return $error;
        }
        if (username_exists($username)) {
            $error->add(406, 'Sorry, that username already exists!', array('status' => 400));
            return $error;
        }
        if (email_exists($email)) {
            $error->add(407,'Sorry, that email already exists!', array('status' => 400));
            return $error;
        }
        if (!empty($phone)) {
            $args  = array(
                'meta_key' => 'phone',
                'meta_value' => $phone //the value to compare against
            );
            $user_query = new WP_User_Query( $args );
            $users = $user_query->get_results();
            if (!empty($users)) {
                $error->add(408,'Sorry, that phone number already exists!', array('status' => 400));
                return $error;
            }
        }
        if ( 5 > strlen( $password ) ) {
            $error->add( 409,'Password length must be greater than 5', array('status' => 400) );
            return $error;
        }
        if ( $password !== $password2 ) {
            $error->add( 410,'Password is not identical', array('status' => 400) );
            return $error;
        }


        $user_id = wp_create_user($username, $password, $email);
        if (!is_wp_error($user_id)) {
            $user = get_user_by('id', $user_id);
            $user->set_role('subscriber');
            update_user_meta($user_id, 'phone', $phone);
            update_user_meta($user_id, 'telephone', $phone);
            update_user_meta($user_id, 'peepso_user_field_146', $occupation);
            $userdata   = $this->MSAPI_get_userData($user);
            $method     = 'POST';
            $url        = home_url() . "/wp-json/jwt-auth/v1/token";
            $data       = array('username' => $username,'password' => $password);
            $token      = $this->getToken($method, $url, $data);

            $response['code'] = 200;
            $response['message'] = __("User '" . $username . "' Registration was Successful", "wp-rest-user");
            $response['data'] = $userdata;
            $response['token'] = $token;
        } else {
            return $user_id;
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
new Ms_api_register();