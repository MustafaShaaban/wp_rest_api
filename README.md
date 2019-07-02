# MS API
This plugin still under development and has been created to handle the whole website API's

#### Index:
    
    1- Login API.
    2- Registration API.
    
    
#### Login:

_**Required data:**_
    
    url = {domain_name}/wp-json/MSAPI/users/login
        body_data:
            user_login      => The provided username / email / phone number
            user_password   => The provided password

_**Sample Code (JS XHR):**_           
            
    var data = new FormData();
    data.append("user_login", "Jhone");
    data.append("user_password", "P@$$W0rD 123");
    
    var xhr = new XMLHttpRequest();
    xhr.withCredentials = true;
    
    xhr.addEventListener("readystatechange", function () {
      if (this.readyState === 4) {
        console.log(this.responseText);
      }
    });
    
    xhr.open("POST", "{domain_name}/wp-json/MSAPI/users/login");
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.setRequestHeader("Cache-Control", "no-cache");
    
    xhr.send(data);

_**Sample Code (jQuery AJAX):**_ 

    var form = new FormData();
    form.append("user_login", "Jhone");
    form.append("user_password", "P@$$W0rD 123");
    
    var settings = {
      "async": true,
      "crossDomain": true,
      "url": "{domain_name}/wp-json/MSAPI/users/login",
      "method": "POST",
      "headers": {
        "Content-Type": "application/x-www-form-urlencoded",
        "cache-control": "no-cache"
      },
      "mimeType": "multipart/form-data",
      "data": form
    }
    
    $.ajax(settings).done(function (response) {
      console.log(response);
    });

_**Response:**_

Response will be JSON.

    {
        "code": 200,
        "message": "The user has been logged in successfully"
        "data": {
            "ID": "6",
            "user_login": "Jhone",
            "user_pass": "$P$BkWTNNc1qG3WNvOhIlmFrG3GxZDO.z/",
            "user_nicename": "jhone",
            "user_email": "jhone@msapi.com",
            "user_url": "",
            "user_registered": "2019-06-30 17:24:10",
            "user_activation_key": "",
            "user_status": "0",
            "display_name": "Jhone",
            "nickname": "Jhone",
            "first_name": "",
            "last_name": "",
            "description": "",
            "rich_editing": "true",
            "syntax_highlighting": "true",
            "comment_shortcuts": "false",
            "admin_color": "fresh",
            "use_ssl": "0",
            "show_admin_bar_front": "true",
            "locale": "",
            "wp_capabilities": "a:1:{s:10:\"subscriber\";b:1;}",
            "wp_user_level": "0",
            "phone": "+201016999700",
            "telephone": "+201016999700",
            "peepso_user_field_146": "option_146_3",
            "session_tokens": "a:1:{s:64:\"9269e18f01ececf4b478e9fd83407f5bdbea442cbd12efc0b175584ebd5211e7\";a:4:{s:10:\"expiration\";i:1562088453;s:2:\"ip\";s:3:\"::1\";s:2:\"ua\";s:21:\"PostmanRuntime/7.15.0\";s:5:\"login\";i:1561915653;}}"
        },
        "token": "TOKEN_KEY",
    }
            







#### Registration:

_**Required data:**_
    
    url = {domain_name}/wp-json/MSAPI/users/register
            body_data:
                username    => The provided username
                email       => The provided email
                password1   => The first password
                password2   => Rewritten password
                phone       => The provided phone number
                occupation  => The provided occupation

_**Sample Code (JS XHR):**_           
            
    var data = new FormData();
        data.append("username", "Jhone");
        data.append("email", "jhone@msapi.com");
        data.append("password1", "P@$$W0rD 123");
        data.append("password2", "P@$$W0rD 123");
        data.append("phone", "+201016999700");
        data.append("occupation", "option_146_3");
        
        var xhr = new XMLHttpRequest();
        xhr.withCredentials = true;
        
        xhr.addEventListener("readystatechange", function () {
          if (this.readyState === 4) {
            console.log(this.responseText);
          }
        });
        
        xhr.open("POST", "{domain_name}/wp-json/MSAPI/users/register");
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.setRequestHeader("Cache-Control", "no-cache");        
        
        xhr.send(data);

_**Sample Code (jQuery AJAX):**_ 

    var form = new FormData();
    form.append("user_login", "Jhone");
    form.append("user_password", "P@$$W0rD 123");
    
    var settings = {
      "async": true,
      "crossDomain": true,
      "url": "{domain_name}/wp-json/MSAPI/users/login",
      "method": "POST",
      "headers": {
        "Content-Type": "application/x-www-form-urlencoded",
        "cache-control": "no-cache"
      },
      "mimeType": "multipart/form-data",
      "data": form
    }
    
    $.ajax(settings).done(function (response) {
      console.log(response);
    });

_**Response:**_

Response will be JSON.

    {
        "code": 200,
        "message": "User 'Jhone' Registration was Successful",
        "data": {
            "ID": "6",
            "user_login": "Jhone",
            "user_pass": "$P$BkWTNNc1qG3WNvOhIlmFrG3GxZDO.z/",
            "user_nicename": "jhone",
            "user_email": "jhone@msapi.com",
            "user_url": "",
            "user_registered": "2019-06-30 17:24:10",
            "user_activation_key": "",
            "user_status": "0",
            "display_name": "Jhone",
            "nickname": "Jhone",
            "first_name": "",
            "last_name": "",
            "description": "",
            "rich_editing": "true",
            "syntax_highlighting": "true",
            "comment_shortcuts": "false",
            "admin_color": "fresh",
            "use_ssl": "0",
            "show_admin_bar_front": "true",
            "locale": "",
            "wp_capabilities": "a:1:{s:10:\"subscriber\";b:1;}",
            "wp_user_level": "0",
            "phone": "+201016999700",
            "telephone": "+201016999700",
            "peepso_user_field_146": "option_146_3"
        },
        "token": "TOKEN_KEY"
    }