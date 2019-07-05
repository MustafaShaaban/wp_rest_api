<?php
/**
 * Created by PhpStorm.
 * User: Mustafa Shaaban
 * Date: 6/20/2019
 * Time: 6:10 PM
 */

class Ms_api_register extends WP_REST_Request
{

    protected $username;
    protected $email;
    protected $password;
    protected $password2;
    protected $phone;
    protected $occupation;
    protected $defaults = [
        'peepso_user_field_gender_acc',
        'peepso_user_field_birthdate_acc',
        'peepso_user_field_description_acc',
        'peepso_user_field_location_acc',
        'peepso_user_field_user_url_acc',
        'peepso_user_field_147_acc',
        'peepso_user_field_148_acc',
        'peepso_user_field_149_acc'
    ];

    public function __construct()
    {
        add_action('rest_api_init', array($this, 'MS_API_register_new_user'));
    }

    public function MS_API_register_new_user($request)
    {
        /**
         * Handle Register User request.
         */
        register_rest_route('MSAPI', 'users/register/(?P<type>\S+)', array(
            'methods' => 'POST',
            'callback' => array($this, 'MS_API_user_endpoint_handler'),
        ));
    }

    /**
     * The function responsible for handling API operations
     *
     * @param null $request
     * @return WP_Error|WP_REST_Response
     */
    public function MS_API_user_endpoint_handler($request = null)
    {
        $parameters = $request->get_body_params();
        $url_params = $request->get_url_params();
        $this->username = sanitize_text_field($parameters['username']);
        $this->email = sanitize_text_field($parameters['email']);
        $this->password = sanitize_text_field($parameters['password1']);
        $this->password2 = sanitize_text_field($parameters['password2']);
        $this->phone = sanitize_text_field($parameters['phone']);
        $this->occupation = sanitize_text_field($parameters['occupation']); // peepso_user_field_146

        switch ($url_params['type']) {

            case 'authentication':
                $validations = $this->validate_body_content();
                if (!empty($validations)) {
                    return $validations;
                }

                $response = $this->otp_authentication();
                return new WP_REST_Response($response, 123);
                break;

            case 'verification':
                $validations = $this->validate_body_content();
                if (!empty($validations)) {
                    return $validations;
                }

                $error = new WP_Error();
                $txId = $parameters['txId'];
                $otp_code = $parameters['otp_code'];
                if (empty($txId)) {
                    $error->add(411, "txId parameter can't be empty!", array('status' => 400));
                    return $error;
                }
                if (empty($otp_code)) {
                    $error->add(412, "otpCode parameter can't be empty!", array('status' => 400));
                    return $error;
                }

                $response = $this->otp_verification($txId, $otp_code);
                $status = $response['status'];
                if ('SUCCESS' == $status) {
                    $response = $this->MSAPI_create_user();
                }
                return new WP_REST_Response($response, 123);
                break;

            case 'reAuth':
                $response = $this->otp_authentication();
                return new WP_REST_Response($response, 123);
                break;

            default:
                $response = array(
                    'code' => 400,
                    'message' => "Can't create this user"
                );
                return new WP_REST_Response($response, 123);
                break;
        }

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
        $error = new WP_Error();
        if (empty($this->username)) {
            $error->add(400, "Username field 'username' is required.", array('status' => 400));
            return $error;
        }
        if (empty($this->email)) {
            $error->add(401, "Email field 'email' is required.", array('status' => 400));
            return $error;
        }
        if (empty($this->password)) {
            $error->add(402, "Password field 'password' is required.", array('status' => 400));
            return $error;
        }
        if (empty($this->phone)) {
            $error->add(403, "Phone number field is required.", array('status' => 400));
            return $error;
        }
        if (empty($this->occupation)) {
            $error->add(404, "occupation field is required.", array('status' => 400));
            return $error;
        }

        if (!validate_username($this->username)) {
            $error->add(405, 'Sorry, the username you entered is not valid', array('status' => 400));
            return $error;
        }
        if (username_exists($this->username)) {
            $error->add(406, 'Sorry, that username already exists!', array('status' => 400));
            return $error;
        }
        if (email_exists($this->email)) {
            $error->add(407, 'Sorry, that email already exists!', array('status' => 400));
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
        }
    }

    /**
     * The function responsible for send otp verification message to the user
     * @return array|int
     *
     * @author Mustafa Shaaban
     * @version 1.0.0 V
     */
    public function otp_authentication()
    {
        /* The challenge rest api url which needs to be called to challenge the user. */
        $generateUrl = "https://login.xecurify.com/moas/api/auth/challenge";
        /* The customer Key provided to you */
        $customerKey = "177305";
        /* The customer API Key provided to you */
        $apiKey = "OdTEiHblSdBbFbd4oCT062A1k2Zx7t1Z";
        /* Current time in milliseconds since midnight, January 1, 1970 UTC. */
        $currentTimeInMillis = round(microtime(true) * 1000);
        /* Creating the Hash using SHA-512 algorithm */
        $stringToHash = $customerKey . number_format($currentTimeInMillis, 0, '', '') .
            $apiKey;
        $hashValue = hash("sha512", $stringToHash);
        /* The Array containing the request information */
        $jsonRequest = array(
            "customerKey" => $customerKey,
            "phone" => $this->phone,
            "email" => $this->email,
            "authType" => "SMS",
            "transactionName" => "CUSTOM-OTP-VERIFICATION"
        );
        /* JSON encode the request array to get JSON String */
        $jsonRequestString = json_encode($jsonRequest);
        $customerKeyHeader = "Customer-Key: " . $customerKey;
        $timestampHeader = "Timestamp: " . number_format($currentTimeInMillis, 0, '', ''
            );
        $authorizationHeader = "Authorization: " . $hashValue;
        /* Initialize curl */
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json",
            $customerKeyHeader, $timestampHeader, $authorizationHeader));
        curl_setopt($ch, CURLOPT_URL, $generateUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonRequestString);
        curl_setopt($ch, CURLOPT_POST, 1);
        /* Calling the rest API */
        $result = curl_exec($ch);
        $err = curl_errno($ch);
        curl_close($ch);
        if ($err) {
            return $err;
        }
        /* If a valid response is received, get the JSON response */
        $otp_response = (array)json_decode($result);
        $response = [
            'code' => 200,
            "message" => $otp_response['message'],
            "status" => $otp_response['status'],
            'data' => [
                'txId' => $otp_response['txId'],
                'authType' => $otp_response['authType'],
                'responseType' => $otp_response['responseType'],
                'phoneDelivery' => $otp_response['phoneDelivery'],
                'emailDelivery' => $otp_response['emailDelivery']
            ],
        ];
        return $response;
    }

    /**
     * The function responsible for validate the user code
     * @param $txId
     * @param $otp_code
     * @return array
     *
     * @author Mustafa Shaaban
     * @version 1.0.0 V
     */
    public function otp_verification($txId, $otp_code)
    {
        /* The challenge rest api url which needs to be called to validate the user. */
        $validateUrl = "https://login.xecurify.com/moas/api/auth/validate";
        /* The customer Key provided to you */
        $customerKey = "177305";
        /* The customer API Key provided to you */
        $apiKey = "OdTEiHblSdBbFbd4oCT062A1k2Zx7t1Z";
        /* Current time in milliseconds since midnight, January 1, 1970 UTC. */
        $currentTimeInMillis = round(microtime(true) * 1000);
        /* Creating the Hash using SHA-512 algorithm */
        $stringToHash = $customerKey . number_format($currentTimeInMillis, 0, '', '') .
            $apiKey;
        $hashValue = hash("sha512", $stringToHash);
        /* The Array containing the validate information */
        $jsonRequest = array(
            'txId' => $txId,
            'token' => $otp_code
        );
        /* JSON encode the request array to get JSON String */
        $jsonRequestString = json_encode($jsonRequest);
        $customerKeyHeader = "Customer-Key: " . $customerKey;
        $timestampHeader = "Timestamp: " . number_format($currentTimeInMillis, 0, '', ''
            );
        $authorizationHeader = "Authorization: " . $hashValue;
        /* Initialize curl */
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json",
            $customerKeyHeader, $timestampHeader, $authorizationHeader));
        curl_setopt($ch, CURLOPT_URL, $validateUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonRequestString);
        curl_setopt($ch, CURLOPT_POST, 1);
        /* Calling the rest API */
        $result = curl_exec($ch);
        $otp_err = curl_errno($ch);
        curl_close($ch);
        if ($otp_err) {
            $err = [
                'code' => 400,
                "message" => $otp_err['message'],
                "status" => $otp_err['status'],
                'data' => [
                    'txId' => $otp_err['txId'],
                    'responseType' => $otp_err['responseType']
                ],
            ];
            return $err;
        }
        /* If a valid response is received, get the JSON response */
        $response = (array)json_decode($result);
        $status = $response['status'];
        if ($status === 'FAILED') {
            $response = [
                'code' => 400,
                "message" => $response['message'],
                "status" => $response['status'],
                'data' => [
                    'txId' => $response['txId'],
                    'responseType' => $response['responseType']
                ],
            ];

        }
        return $response;
    }

    /**
     * The function responsible for creating the user
     * @return array
     *
     * @author Mustafa Shaaban
     * @version 1.0.0 V
     */
    public function MSAPI_create_user()
    {
        $user_id = wp_create_user($this->username, $this->password, $this->email);
        if (!is_wp_error($user_id)) {
            $user = get_user_by('id', $user_id);
            $user->set_role('subscriber');
            $this->update_profile_meta($user_id);
            $user_data = $this->MSAPI_get_userData($user);
            $method = 'POST';
            $url = home_url() . "/wp-json/jwt-auth/v1/token";
            $data = array('username' => $this->username, 'password' => $this->password);
            $token = $this->getToken($method, $url, $data);

            $response = array(
                'code' => 200,
                'message' => "User '" . $this->username . "' Registration was Successful",
                'data' => $user_data,
                'token' => $token
            );

        } else {
            $response = array(
                'code' => 400,
                'message' => "Can't create this user"
            );
        }
        return $response;
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
     * The function responsible for get the user data
     * @param $user
     * @return object
     *
     * @author Mustafa Shaaban
     * @version 1.0.0 V
     */
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

    /**
     * The function responsible for update the user meta
     * @param $user_id
     *
     * @author Mustafa Shaaban
     * @version 1.0.0 V
     */
    public function update_profile_meta($user_id) {
        foreach ($this->defaults as $meta) {
            update_user_meta($user_id, $meta, '10');
        }
        update_user_meta($user_id, 'phone', $this->phone);
        update_user_meta($user_id, 'telephone', $this->phone);
        update_user_meta($user_id, 'peepso_user_field_146', $this->occupation);
    }

}

new Ms_api_register();