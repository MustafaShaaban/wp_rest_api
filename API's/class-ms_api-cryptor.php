<?php
/**
 * Created by PhpStorm.
 * User: Mustafa Shaaban
 * Date: 6/20/2019
 * Time: 6:10 PM
 */

class Ms_api_cryptor
{
    protected $method = 'aes-128-ctr'; // default cipher method if none supplied
    private $key;

    protected function iv_bytes()
    {
        return openssl_cipher_iv_length($this->method);
    }

    public function __construct($key = FALSE, $method = FALSE)
    {
        if(!$key) {
            $key = md5('Emk!On&HSGdMtN)S1%Jx*O4j'); // default encryption key if none supplied
        }
        if(ctype_print($key)) {
            // convert ASCII keys to binary format
            $this->key = openssl_digest($key, 'SHA256', TRUE);
        } else {
            $this->key = $key;
        }
        if($method) {
            if(in_array(strtolower($method), openssl_get_cipher_methods())) {
                $this->method = $method;
            } else {
                die(__METHOD__ . ": unrecognised cipher method: {$method}");
            }
        }

        add_action('rest_api_init', array($this, 'MS_API_cryptor'));
    }

    public function MS_API_cryptor($request){

        /**
         * Handle encryption request.
         */
        register_rest_route('MSAPI', 'cryptor/(?P<type>\S+)', array(
            'methods' => 'POST',
            'callback' => array($this, 'MS_API_handle_request'),
        ));

    }

    public function MS_API_handle_request($request = null) {
        $error = new WP_Error();
        $response = array();
        $url_params = $request->get_url_params();
        $body_params = $request->get_body_params();

        if (empty($url_params['type'])) {
            $error->add(400, "Please provide the correct type!", array('status' => 400));
            return $error;
        }

        if (empty($body_params['data'])) {
            $error->add(400, "Data can't be empty!", array('type_empty' => 400));
            return $error;
        }

        switch ($url_params['type']) {
            case 'enc':
                $data = $this->encrypt($body_params['data']);
                $response = [
                    'code' => 200,
                    'data' => $data,
                    'msg' => 'Data has been encrypted successfully',
                ];
                break;
            case 'dec':
                $data  = $this->decrypt($body_params['data']);
                $response = [
                    'code' => 200,
                    'data' => $data,
                    'msg' => 'Data has been decrypted successfully',
                ];
                break;
            default:
                $error->add(400, "Please provide the correct type!", array('status' => 400));
                return $error;
        }

        return new WP_REST_Response($response, 123);
    }

    // encrypt encrypted string
    public function encrypt($data)
    {
        $iv = openssl_random_pseudo_bytes($this->iv_bytes());
        if (is_array($data)) {
            $data = serialize($data);
        }
        $enc = bin2hex($iv) . openssl_encrypt($data, $this->method, $this->key, 0, $iv);
        return $enc;
    }

    // decrypt encrypted string
    public function decrypt($data)
    {
        $iv_strlen = 2  * $this->iv_bytes();
        if(preg_match("/^(.{" . $iv_strlen . "})(.+)$/", $data, $regs)) {
            list(, $iv, $crypted_string) = $regs;
            if(ctype_xdigit($iv) && strlen($iv) % 2 == 0) {
                $dec = openssl_decrypt($crypted_string, $this->method, $this->key, 0, hex2bin($iv));
                return unserialize($dec);
            }
        }
        return FALSE; // failed to decrypt
    }

    public function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

}
