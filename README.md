# MS API's
**API's**

**Login API:**

    url = domain_name/wp-json/MSAPI/users/login
    body_data
        * user_login => The provided username
        * user_password => The provided password
        
    Response
        success
            array (
                'code'      => 200,
                'token'     => token,
                'data'      => user data,
                'message'   => 'The user has been logged in successfully'
            )
        False
            array (
                'code' => 400,
                'message' => 'The message'
            }
            
**Registration API:**

    url = domain_name/wp-json/MSAPI/users/register
        body_data
            * username => The provided username
            * email => The provided email
            * password1 => The first password
            * password2 => Rewritten password
            * phone => The provided phone number
            * occupation => The provided occupation
            
    Response
        success
            array (
                'code'      => 200,
                'token'     => token,
                'data'      => user data,
                'message'   => 'User "usernam" has Registration was Successful'
            )
        False
            array (
                'code' => 400,
                'message' => 'The message'
            }
