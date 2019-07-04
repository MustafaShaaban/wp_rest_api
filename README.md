# MS API
This plugin still under development and has been created to handle the whole website API's

#### Index:

    1- Login API.
    2- Registration API.
    3- Occupation API.
    
---
## 1- Login:


_**Required data:**_
    
    url = {domain_name}/wp-json/MSAPI/users/login
        body_data:
            user_login      => The provided username / email / phone number
            user_password   => The provided password

_**Sample Code (JS XHR):**_           
            
    var data = new FormData();
    data.append("user_login", "John");
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
    form.append("user_login", "John");
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

The following is the JSON Response generated by the Rest API.

    {
        "code": 200,
        "message": "The user has been logged in successfully"
        "data": {
            "ID": "6",
            "user_login": "John",
            "user_pass": "$P$BkWTNNc1qG3WNvOhIlmFrG3GxZDO.z/",
            "user_nicename": "John",
            "user_email": "John@msapi.com",
            "user_url": "",
            "user_registered": "2019-06-30 17:24:10",
            "user_activation_key": "",
            "user_status": "0",
            "display_name": "John",
            "nickname": "John",
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
 

---
## 2- Registration:

This API responsible for Register users and send OTP verification code to verify the phone number of this user.
There is two steps to call this API.

_***STEP 1: (CREATE AUTHENTICATION CODE)***_

To create the authentication code and send it to the phone number; You need to make a HTTP POST request to: **"{domain name}/wp-json/MSAPI/users/register/authentication"**

    The Required data:
        username    => The provided username
        email       => The provided email
        password1   => The first password
        password2   => The rewritten password
        phone       => The provided phone number
        occupation  => The provided occupation
        
The REST API will check if this is a valid data or not and then, Will send the authentication code to the phone number if it valid.

**Sample Code (JS XHR):**          
            
    var data = new FormData();
        data.append("username", "John");
        data.append("email", "John@msapi.com");
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
        
        xhr.open("POST", "{domain_name}/wp-json/MSAPI/users/register/authentication");
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.setRequestHeader("Cache-Control", "no-cache");        
        
        xhr.send(data);

**Sample Code (jQuery AJAX):**

    var form = new FormData();
    data.append("username", "John");
    data.append("email", "John@msapi.com");
    data.append("password1", "P@$$W0rD 123");
    data.append("password2", "P@$$W0rD 123");
    data.append("phone", "+201016999700");
    data.append("occupation", "option_146_3");
    
    var settings = {
      "async": true,
      "crossDomain": true,
      "url": "{domain_name}/wp-json/MSAPI/users/register/authentication",
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

**Response:**

The following is the JSON Response generated by the Rest API.

    {
        "txId": "txId_code",
        "authType": "SMS",
        "responseType": "CHALLENGE",
        "phoneDelivery": {
            "contact": "20101699970",
            "sendStatus": "SUCCESS",
            "sendTime": "07-03-2019 22:32:45"
        },
        "emailDelivery": {
            "contact": "m.s@a.s",
            "sendStatus": "SUCCESS",
            "sendTime": "07-03-2019 22:32:44"
        },
        "status": "SUCCESS",
        "message": "Successfully generated."
    }

_***STEP 2: (VERIFY AUTHENTICATION CODE / REGISTER USER)***_

To verify the code that has been already sent before at the first step; You need to make a HTTP POST request to: **"{domain name}/wp-json/MSAPI/users/register/verification"**

    The Required data:
        username    => The provided username
        email       => The provided email
        password1   => The first password
        password2   => The rewritten password
        phone       => The provided phone number
        occupation  => The provided occupation
        occupation  => The provided occupation
        txId        => The txId code from the first call,
        otp_code    => The code number that has been sent to the provided phone number
        
The REST API will check again if this is a valid data or not and then, if it valid and the code is valid too, Will save the user data into the database.

**Sample Code (JS XHR):**          
            
    var data = new FormData();
        data.append("username", "John");
        data.append("email", "John@msapi.com");
        data.append("password1", "P@$$W0rD 123");
        data.append("password2", "P@$$W0rD 123");
        data.append("phone", "+201016999700");
        data.append("occupation", "option_146_3");
        data.append("txId", "txId_code");
        data.append("otp_code", "code");
        
        var xhr = new XMLHttpRequest();
        xhr.withCredentials = true;
        
        xhr.addEventListener("readystatechange", function () {
          if (this.readyState === 4) {
            console.log(this.responseText);
          }
        });
        
        xhr.open("POST", "{domain_name}/wp-json/MSAPI/users/register/verification");
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.setRequestHeader("Cache-Control", "no-cache");        
        
        xhr.send(data);

**Sample Code (jQuery AJAX):**

    var form = new FormData();
    data.append("username", "John");
    data.append("email", "John@msapi.com");
    data.append("password1", "P@$$W0rD 123");
    data.append("password2", "P@$$W0rD 123");
    data.append("phone", "+201016999700");
    data.append("occupation", "option_146_3");
    data.append("txId", "txId_code");
    data.append("otp_code", "code");
    
    var settings = {
      "async": true,
      "crossDomain": true,
      "url": "{domain_name}/wp-json/MSAPI/users/register/verification",
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
    
**Response:**

The following is the JSON Response generated by the Rest API.

    {
        "code": 200,
        "message": "User 'John' Registration was Successful",
        "data": {
            "ID": "6",
            "user_login": "John",
            "user_pass": "$P$BkWTNNc1qG3WNvOhIlmFrG3GxZDO.z/",
            "user_nicename": "John",
            "user_email": "John@msapi.com",
            "user_url": "",
            "user_registered": "2019-06-30 17:24:10",
            "user_activation_key": "",
            "user_status": "0",
            "display_name": "John",
            "nickname": "John",
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
    
**NOTE:**

In case You want to resend a new verification code, You will need to make a HTTP POST request to: **"{domain name}/wp-json/MSAPI/users/register/reAuth"**, with the same data in STEP1.
 

---
## 3- Occupation API

This REST API to get all occupation values from the website, All you need to do is to make a HTTP POST request to: **"{domain name}/wp-json/MSAPI/forms/occupation"**

**Response:**

The following is the JSON Response generated by the Rest API.
    
    {
        "code": 200,
        "data": {
            "option_146_1": "Player",
            "option_146_2": "Fan",
            "option_146_3": "Technical Coach",
            "option_146_4": "Head Coach",
            "option_146_5": "Referee",
            "option_146_7": "Nutri Doctor",
            "option_146_9": "Trainer",
            "option_146_10": "Media Person",
            "option_146_11": "Organizer",
            "option_146_12": "Broadcaster",
            "option_146_13": "Managerial",
            "option_146_14": "Board Member"
        }
    }
