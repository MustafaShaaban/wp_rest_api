# wp_rest_api
API's

Login API:

    url = domain_name/wp-json/MSAPI/stars/login
    body_data
        * user_login => The provided username
        * user_password => The provided password
        
Response

    success
    
        array (
            'code' => 200,
            'data' => $userdata,
            'message' => 'The user has been logged in successfully'
        )
        
    False
    
        array (
            'code' => 400,
            'message' => 'The message'
        }