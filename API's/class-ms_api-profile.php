<?php
/**
 * Created by PhpStorm.
 * User: Mustafa Shaaban
 * Date: 6/20/2019
 * Time: 6:10 PM
 */

class Ms_api_profile_about extends WP_REST_Request
{
    public function __construct()
    {
        add_action('rest_api_init', array($this, 'MS_API_update_profile_about'));
    }

    public function MS_API_update_profile_about($request)
    {
        /**
         * Handle Register User request.
         */
        register_rest_route('MSAPI', 'users/profile/(?P<type>\S+)', array(
            'methods' => 'POST',
            'callback' => array($this, 'MS_API_profile_endpoint_handler'),
        ));
    }

    /**
     * The function responsible for handling API operations
     *
     * @param null $request
     * @return WP_Error|WP_REST_Response
     */
    public function MS_API_profile_endpoint_handler($request = null)
    {
        $response = array();
        $parameters = $request->get_body_params();
        $url_params = $request->get_url_params();
        $this->validate_body_content();

        switch ($url_params['type']) {
            case 'about':
                break;
            default:
                break;
        }

        return new WP_REST_Response($response, 123);
    }



    /**
     * The function responsible for validate the body content (Received Form Content)
     * @return WP_Error
     *
     * @author Mustafa Shaaban
     * @version 1.0.0 V
     */
    public function validate_body_content()
    {
        /*$error = new WP_Error();
        if (empty($user_ID)) {
            $error->add(400, "The user ID mustn't be empty", array('status' => 400));
            return $error;
        }
        if (!empty($this->phone)) {
            $args = array(
                'meta_key' => 'phone',
                'meta_value' => $this->phone //the value to compare against
            );
            $user_query = new WP_User_Query($args);
            $users = $user_query->get_results();
            if (!empty($users)) {
                $error->add(408, 'Sorry, that phone number already exists!', array('status' => 400));
                return $error;
            }
        }
        if (5 > strlen($this->password)) {
            $error->add(409, 'Password length must be greater than 5', array('status' => 400));
            return $error;
        }
        if ($this->password !== $this->password2) {
            $error->add(410, 'Password is not identical', array('status' => 400));
            return $error;
        }*/
    }

    /**
     * The function responsible for getting the token for the provided user
     * @param $method
     * @param $url
     * @param bool $data
     * @return array|bool|mixed|object|string
     *
     * @author Mustafa Shaaban
     * @version 1.0.0 V
     */
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
            return $err;
        } else {
            return json_decode($response);
        }
    }

}

new Ms_api_profile_about();