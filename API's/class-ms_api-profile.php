<?php
/**
 * Created by PhpStorm.
 * User: Mustafa Shaaban
 * Date: 6/20/2019
 * Time: 6:10 PM
 */

class Ms_api_profile_about extends WP_REST_Request
{
    private $user_ID;
    private $meta_name = [];
    private $user_basics = [];
    public function __construct()
    {
        add_action('rest_api_init', array($this, 'MS_API_update_profile_about'));
    }

    public function MS_API_update_profile_about($request)
    {
        /**
         * Handle Register User request.
         */
        register_rest_route('MSAPI', 'users/profile/(?P<type>\S+)/(?P<id>[\d]+)', array(
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
        $response = [];
        $parameters = $request->get_body_params();
        $url_params = $request->get_url_params();
        $this->validate_url_id($url_params['id']);
        if (empty($parameters)) {
            $response = [
                'code' => 200,
                'message' => 'The form body can\'t be empty'
            ];
            return new WP_REST_Response($response, 123);
        }
        switch ($url_params['type']) {
            case 'about':
                $validations = $this->validate_about_content($parameters);
                if (!empty($validations)) {
                    return $validations;
                }
                $response = $this->update_about_content();
                break;
            case 'privacy':
                $validations = $this->validate_about_privacy($parameters);
                if (!empty($validations)) {
                    return $validations;
                }
                $response = $this->update_about_privacy($parameters);
                break;
            default:
                break;
        }

        return new WP_REST_Response($response, 123);
    }

    /**
     * The function responsible for validate the user ID from comes from url.
     *
     * @param $id
     * @return int|WP_REST_Response
     *
     * @author Mustafa Shaaban
     * @version 1.0.0 V
     */
    public function validate_url_id($id){
        $user_ID = (int)$id;
        $user = get_user_by('ID', $user_ID);
        if (!$user) {
            $response = [
                'code' => 400,
                'msg' => 'Invalid user ID'
            ];
            return new WP_REST_Response($response, 123);
        }
        return $this->user_ID = $user->ID;
    }

    /**
     * The function responsible for validate the body content (Received Form Content) for about type.
     * @return WP_Error
     *
     * @author Mustafa Shaaban
     * @version 1.0.0 V
     */
    public function validate_about_content($parameters)
    {
        $param = $parameters;
        $error = new WP_Error();

        if (isset($param['username'])) {
            $new_username = sanitize_text_field($param['username']);
            if (empty($new_username)) {
                $error->add(400, "Username field is required.", array('status' => 400));
                return $error;
            }
            $username = get_user_by('login', $new_username);
            if ($username && $username->ID !== $this->user_ID) {
                $error->add(400, "This username is already exists.", array('status' => 400));
                return $error;
            }
            $this->user_basics = ['username' => $new_username];
        }
        if (isset($param['email'])) {
            $new_email = sanitize_text_field($param['email']);
            if (empty($new_email)) {
                $error->add(400, "Username field is required.", array('status' => 400));
                return $error;
            }
            $email = get_user_by('email', $new_email);
            if ($email && $email->ID !== $this->user_ID) {
                $error->add(400, "This email is already exists.", array('status' => 400));
                return $error;
            }
            $this->user_basics = ['email' => $new_email];
        }
        if (isset($param['password'])) {
            $password = sanitize_text_field($param['password']);
            if (empty($password)) {
                $error->add(400, "Password field is required.", array('status' => 400));
                return $error;
            }
            if (5 > strlen($password)) {
                $error->add(409, 'Password length must be greater than 5', array('status' => 400));
                return $error;
            }
            $this->user_basics = ['password' => $password];
        }

        if (isset($param['first_name'])) {
            $first_name = sanitize_text_field($param['first_name']);
            if (empty($first_name)) {
                $error->add(400, "First name field is required.", array('status' => 400));
                return $error;
            }
            $this->meta_name = [
                'meta_key' => 'first_name',
                'meta_value' => $first_name,
            ];
        }
        if (isset($param['last_name'])) {
            $last_name = sanitize_text_field($param['last_name']);
            if (empty($last_name)) {
                $error->add(400, "Last name field is required.", array('status' => 400));
                return $error;
            }
            $this->meta_name = [
                'meta_key' => 'last_name',
                'meta_value' => $last_name,
            ];
        }
        if (isset($param['occupation'])) {
            $occupation = sanitize_text_field($param['occupation']);
            if (empty($occupation)) {
                $error->add(400, "Occupation field is required.", array('status' => 400));
                return $error;
            }
            $this->meta_name = [
                'meta_key' => 'peepso_user_field_146',
                'meta_value' => $occupation,
            ];
        }
        if (isset($param['phone'])) {
            $phone = sanitize_text_field($param['phone']);
            if (empty($phone)) {
                $error->add(400, "Phone number field is required.", array('status' => 400));
                return $error;
            }
            $user_query = new WP_User_Query(array(
                'meta_key' => 'phone',
                'meta_value' => $phone
            ));
            $users = $user_query->get_results();
            if (!empty($users)) {
                $error->add(408, 'Sorry, that phone number already exists!', array('status' => 400));
                return $error;
            }
            $this->meta_name = [
                'meta_key' => 'phone',
                'meta_value' => $phone,
            ];
        }
        if (isset($param['description'])) {
            $description= sanitize_text_field($param['description']);
            if (empty($description)) {
                $error->add(400, "About me field is required.", array('status' => 400));
                return $error;
            }
            $this->meta_name = [
                'meta_key' => 'description',
                'meta_value' => $description,
            ];
        }
        if (isset($param['location'])) {
            $location = (array)$param['location'];
            if (empty($location) || count($location) < 3) {
                $error->add(400, "Invalid location!.", array('status' => 400));
                return $error;
            }
            $this->meta_name = [
                'meta_key' => 'peepso_user_field_location',
                'meta_value' => $location,
            ];
        }
        if (isset($param['country'])) {
            $country = sanitize_text_field($param['country']);
            if (empty($country)) {
                $error->add(400, "Country field is required.", array('status' => 400));
                return $error;
            }
            $this->meta_name = [
                'meta_key' => 'peepso_user_field_147',
                'meta_value' => $country,
            ];
        }
        if (isset($param['gender'])) {
            $gender = sanitize_text_field($param['gender']);
            if (empty($gender)) {
                $error->add(400, "Gender field is required.", array('status' => 400));
                return $error;
            }
            $this->meta_name = [
                'meta_key' => 'peepso_user_field_gender',
                'meta_value' => $gender,
            ];
        }
        if (isset($param['birth_date'])) {
            $birth_date = sanitize_text_field($param['birth_date']);
            if (empty($birth_date)) {
                $error->add(400, "Date of Birth field is required.", array('status' => 400));
                return $error;
            }
            $this->meta_name = [
                'meta_key' => 'peepso_user_field_birthdate',
                'meta_value' => $birth_date,
            ];
        }
        if (isset($param['sports'])) {
            $sports = (array)$param['sports'];
            if (empty($sports)) {
                $error->add(400, "Favorite Sports field is required.", array('status' => 400));
                return $error;
            }
            $this->meta_name = [
                'meta_key' => 'peepso_user_field_148',
                'meta_value' => $sports,
            ];
        }
        if (isset($param['clubs'])) {
            $clubs = (array)$param['clubs'];
            if (empty($clubs)) {
                $error->add(400, "Favorite Clubs field is required.", array('status' => 400));
                return $error;
            }
            $this->meta_name = [
                'meta_key' => 'peepso_user_field_149',
                'meta_value' => $clubs,
            ];
        }
    }

    /**
     * The function responsible for validate the body content (Received Form Content) for about type.
     * @return WP_Error
     *
     * @author Mustafa Shaaban
     * @version 1.0.0 V
     */
    public function validate_about_privacy($parameters) {
        $error = new WP_Error();
        $filed_name = sanitize_text_field($parameters['field_name']);
        $code = (int)$parameters['code'];
        if (empty($filed_name)) {
            $error->add(400, "Field name mustn\'t be empty!.", array('status' => 400));
            return $error;
        }
        if (empty($code)) {
            $error->add(400, "Privacy code mustn\'t be empty!.", array('status' => 400));
            return $error;
        }
    }

    /**
     * The function responsible for update the user About page content.
     *
     * @return array
     *
     * @author Mustafa Shaaban
     * @version 1.0.0 V
     */
    public function update_about_content() {
        $response = array(
            'code' => 400,
            'message' => "Something went wrong, Can't update this user profile."
        );

        if (!empty($this->user_basics)){
            if (isset($this->user_basics['username'])) {
                $user_id = wp_update_user( array( 'ID' => $this->user_ID, 'user_login' => $this->user_basics['username'] ) );
                if ( is_wp_error( $user_id ) ) {
                    return $user_id->get_error_message();
                } else {
                    $response = $this->get_response("User profile has been updated successfully!.");
                }
            }
            if (isset($this->user_basics['email'])) {
                $user_id = wp_update_user( array( 'ID' => $this->user_ID, 'user_email' => $this->user_basics['email'] ) );
                if ( is_wp_error( $user_id ) ) {
                    return $user_id->get_error_message();
                } else {
                    $response = $this->get_response("User profile has been updated successfully!.");
                }
            }
            if (isset($this->user_basics['password'])) {
                wp_set_password( $this->user_basics['password'], $this->user_ID );
                $response = $this->get_response("User profile has been updated successfully!.");
            }
        }

        if (!empty($this->meta_name)){
            $update = update_user_meta($this->user_ID, $this->meta_name['meta_key'], $this->meta_name['meta_value']);
            if ($this->meta_name['meta_key'] === 'phone') {
                update_user_meta($this->user_ID, 'telephone', $this->meta_name['meta_value']);
            }
            if ($update) {
                $response = $this->get_response("User profile has been updated successfully!.");
            }
        }

        return $response;
    }

    /**
     * The function responsible for updating the privacy of the user data
     * @param $parameters
     * @return array
     *
     * @author Mustafa Shaaban
     * @version 1.0.0 V
     */
    public function update_about_privacy($parameters) {
        $response = [
            'code' => 400,
            'message' => 'There is nothing to be change!.'
        ];
        $filed_name = sanitize_text_field($parameters['field_name']);
        $code = (int)$parameters['code'];
        $privacy = [
            '10' => 'Public',
            '20' => 'Site Members',
            '30' => 'Friends Only',
            '40' => 'Only Me'
        ];

        if ($filed_name === 'gender') {
            $update = update_user_meta($this->user_ID, 'peepso_user_field_gender_acc', $code);
            if ($update) {
                $response = $this->get_response("The Privacy has been changed to $privacy[$code] successfully!.");
            }
        } elseif ($filed_name === 'birthdate') {
            $update = update_user_meta($this->user_ID, 'peepso_user_field_birthdate_acc', $code);
            if ($update) {
                $response = $this->get_response("The Privacy has been changed to $privacy[$code] successfully!.");
            }
        } elseif ($filed_name === 'description') {
            $update = update_user_meta($this->user_ID, 'peepso_user_field_description_acc', $code);
            if ($update) {
                $response = $this->get_response("The Privacy has been changed to $privacy[$code] successfully!.");
            }
        } elseif ($filed_name === 'location') {
            $update = update_user_meta($this->user_ID, 'peepso_user_field_location_acc', $code);
            if ($update) {
                $response = $this->get_response("The Privacy has been changed to $privacy[$code] successfully!.");
            }
        } elseif ($filed_name === 'website') {
            $update = update_user_meta($this->user_ID, 'peepso_user_field_user_url_acc', $code);
            if ($update) {
                $response = $this->get_response("The Privacy has been changed to $privacy[$code] successfully!.");
            }
        } elseif ($filed_name === 'country') {
            $update = update_user_meta($this->user_ID, 'peepso_user_field_147_acc', $code);
            if ($update) {
                $response = $this->get_response("The Privacy has been changed to $privacy[$code] successfully!.");
            }
        } elseif ($filed_name === 'sports') {
            $update = update_user_meta($this->user_ID, 'peepso_user_field_148_acc', $code);
            if ($update) {
                $response = $this->get_response("The Privacy has been changed to $privacy[$code] successfully!.");
            }
        } elseif ($filed_name === 'clubs') {
            $update = update_user_meta($this->user_ID, 'peepso_user_field_149_acc', $code);
            if ($update) {
                $response = $this->get_response("The Privacy has been changed to $privacy[$code] successfully!.");
            }
        }
        return $response;
    }

    /**
     * The function responsible for get the user data
     * @param $user
     * @return object
     *
     * @author Mustafa Shaaban
     * @version 1.0.0 V
     */
    public function MSAPI_get_userData()
    {
        $user = get_user_by('id', $this->user_ID);
        $user_basics = $user->data;
        $user_meta = get_user_meta($user->ID);
        $object = new stdClass();
        foreach ($user_meta as $key => $value) {
            $object->$key = $value[0];
        }
        $data = (object)array_merge((array)$user_basics, (array)$object);
        return $data;
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

    /**
     * The function responsible for get the userdata and token and return $response
     * @param string $msg
     * @return array
     *
     * @author Mustafa Shaaban
     * @version 1.0.0 V
     */
    public function get_response($msg = ''){
        $user_data = $this->MSAPI_get_userData();
        $method = 'POST';
        $url = home_url() . "/wp-json/jwt-auth/v1/token";
        $data = array('username' => $user_data->user_login, 'password' => $user_data->user_pass);
        $token = $this->getToken($method, $url, $data);
        $response = array(
            'code' => 200,
            'message' => $msg,
            'data' => $user_data,
            'token' => $token
        );
        return $response;
    }
}

new Ms_api_profile_about();