<?php
/*
 * Sample application for Google+ client to server authentication.
 * Remember to fill in the OAuth 2.0 client id and client secret,
 * which can be obtained from the Google Developer Console at
 * https://code.google.com/apis/console
 *
 * Copyright 2013 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/google-api-php-client/src/Google_Client.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Simple server to demonstrate how to use Google+ Sign-In and
 * verify ID Tokens and Access Tokens on your backend server. 
 *
 * @author cartland@google.com (Chris Cartland)
 */

/**
 * Replace this with the client ID you got from the Google APIs console.
 */
const CLIENT_ID = 'YOUR_CLIENT_ID';

/**
 * Replace this with the client secret you got from the Google APIs console.
 */
const CLIENT_SECRET = 'YOUR_CLIENT_SECRET';

/**
  * Optionally replace this with your application's name.
  */
const APPLICATION_NAME = "Google+ PHP Token Verification";

$client = new Google_Client();
$client->setApplicationName(APPLICATION_NAME);
$client->setClientId(CLIENT_ID);
$client->setClientSecret(CLIENT_SECRET);

$app = new Silex\Application();
$app['debug'] = true;

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__,
));
$app->register(new Silex\Provider\SessionServiceProvider());

// Initialize a session for the current user, and render index.html.
$app->get('/', function () use ($app) {
    return $app['twig']->render('index.html', array(
        'CLIENT_ID' => CLIENT_ID,
        'APPLICATION_NAME' => APPLICATION_NAME
    ));
});

// Verify an ID Token or an Access Token.
// Example URI: /verify?id_token=...&access_token=...
$app->post('/verify', function (Request $request) use($app, $client) {

    $id_token = $request->get("id_token");
    $access_token = $request->get("access_token");

    $token_status = Array();

    $id_status = Array();
    if (!empty($id_token)) {
      // Check that the ID Token is valid.
      try {
        // Client library can verify the ID token.
        $jwt = $client->verifyIdToken($id_token, CLIENT_ID)->getAttributes();
        $gplus_id = $jwt["payload"]["sub"];

        $id_status["valid"] = true;
        $id_status["gplus_id"] = $gplus_id;
        $id_status["message"] = "ID Token is valid.";
      } catch (Google_AuthException $e) {
        $id_status["valid"] = false;
        $id_status["gplus_id"] = NULL;
        $id_status["message"] = "Invalid ID Token.";
      }
      $token_status["id_token_status"] = $id_status;
    }

    $access_status = Array();
    if (!empty($access_token)) {
      $access_status["valid"] = false;
      $access_status["gplus_id"] = NULL;
      // Check that the Access Token is valid.
      $reqUrl = 'https://www.googleapis.com/oauth2/v1/tokeninfo?access_token=' .
              $access_token;
      $req = new Google_HttpRequest($reqUrl);

      $tokenInfo = json_decode(
          $client::getIo()->authenticatedRequest($req)
              ->getResponseBody());

      if ($tokenInfo->error) {
        // This is not a valid token.
        $access_status["message"] = "Invalid Access Token.";
      } else if ($tokenInfo->audience != CLIENT_ID) {
        // This is not meant for this app. It is VERY important to check
        // the client ID in order to prevent man-in-the-middle attacks.
        $access_status["message"] = "Access Token not meant for this app.";
      } else {
        $access_status["valid"] = true;
        $access_status["gplus_id"] = $tokenInfo->user_id;
        $access_status["message"] = "Access Token is valid.";
      }
      $token_status["access_token_status"] = $access_status;
    }

    return $app->json($token_status, 200);
});

$app->run();
