# Verifying Google+ Tokens in PHP

This sample demonstrates how to verify that the ID tokens and access tokens that you receive on your server are valid. This process is important to perform when your app must send tokens to your server but  is unable to use the one-time-code flow for securely getting tokens for your server.

## Security concerns

ID tokens and access tokens are sensitive and can be misused if intercepted. You must ensure that these tokens are handled securely by only transmitting them over HTTPS and only via POST data or within request headers. If you store them on your server, you must also store them securely.

## Use cases

The following are common situations where you might send tokens to your server:

* Sending ID tokens with requests that need to be authenticated. For example, if you need to pass data to your server and you want to ensure that particular data came from a specific user.
* Sending client-side access tokens to the server so that the server can make requests to the Google APIs and when the one-time-code flow is not available. For example, if your iOS app has a back-end server that needs to request data from the APIs and then background process it on behalf of the client.

## When to verify tokens

All tokens need to be verified on your server unless you know that they came directly from Google. Any token that you receive from your client apps must be verified.

## System requirements

PHP 5.3 or later and a web server. [PHP 5.4 and later](http://php.net/downloads.php)
includes a built-in web server.

*Note: The PHP client library requires that PHP has curl extensions configured.*

## Step 1: Enable the Google+ API

Create a Google APIs Console project, OAuth 2.0 client ID, and register your
JavaScript origins:

1.  In the [Google APIs Console](https://developers.google.com/console), select
    **Create** from the pull-down menu on the left, and enter a project name
    (such as "Sample").
1.  In the [Services pane](https://code.google.com/apis/console/?api=plus#:services),
    enable the **Google+ API** and any other APIs that your app requires.
1.  In the [API Access](https://code.google.com/apis/console/#:access)
    pane, click **Create an OAuth 2.0 Client ID**.
    
    1. In the **Product name** field, enter a name for your application
        (such as "Sample"), and click **Next**. Providing a product logo is optional.
    1. In the **Client ID** Settings section, do the following:
          * Select **Web application** for the Application type.
          * Click the **more options** link.
          * In the **Authorized Redirect URIs** field, delete the example URI.
          * In the **Authorized JavaScript Origins** field, add the
              first of the following URLs for development. The last example is of a production URL.
              * `http://localhost:4567`
              * `https://mysite.example.com` 
          * Click the **Create client ID** button.

1.  In the [API Access pane](https://code.google.com/apis/console/#:access),
    locate the section **Client ID for web applications** and note or copy
    the **Client ID** and **Client secret** that you will need later to
    run the sample

## Step 2: Set up the PHP token verification app

1. Get the latest version of the token verification app. One way is to use git to clone
   the application repository.

        git clone https://github.com/googleplus/gplus-verifytoken-php.git

      Or, [download the application as a zip file](https://github.com/googleplus/gplus-verifytoken-php/archive/master.zip),
      and extract the library and sample code:

        wget https://github.com/googleplus/gplus-verifytoken-php/archive/master.zip
        unzip gplus-verifytoken-php-master.zip

1. If you are not using PHP's built-in web server, move the sample app directory
   to a location that your web server can host files, for example:

        mv gplus-verifytoken-php /var/www

1. Change to the directory where the sample app is located.

        cd gplus-verifytoken-php

1. Edit `verify.php`, and replace `YOUR_CLIENT_ID` and
   with the values that you generated in Step 1.
1. Install Composer:

        curl -s https://getcomposer.org/installer | php

1. Execute composer to install the token verification app dependencies:

        php composer.phar install


## Step 3: Run the application

After you set up your Google API project and configured the sample app,
the app is ready to run.

Because you registered `http://localhost:4567` as an **Authorized JavaScript
origin** in the [Google APIs Console](//code.google.com/apis/console/#:access),
you will run the sample from that location.

If you are using the built-in PHP web server, start it with the following
command:

        php -S localhost:4567

Next, browse to your token verification app, loading `verify.php` from the URL where
you hosted it:

`http://localhost:4567/verify.php`

If you are using another web server, make sure to set permissions on the files
in the folder to include execute permissions for verify.php and read permissions
for the required libraries.

        chmod +x /var/www/gplus-verifytoken-php/verify.php
        chmod -R 555 /var/www/gplus-verifytoken-php/vendor

Next, browse to your token verification app, loading `verify.php` from the URL where
you hosted it:

`http://localhost/gplus-verifytoken-php/verify.php`

## Alternatives

You should use the one-time-code flow to get your server its own access tokens and refresh tokens for the user. This one-time-use code is exchanged for tokens and then becomes immediately invalid. It can only be exchanged by server's that have the correct client ID and client secret. These two aspects of the one-time-code flow provide significantly more security over the exchange of tokens with a server.

One-time-code flow is available for web apps and Android apps:
+ [Android](https://developers.google.com/+/mobile/android/sign-in#server-side_access_for_your_app)
+ [Web](https://developers.google.com/+/web/signin/server-side-flow)

