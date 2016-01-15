# Twitch Alerts API Wrapper


## Introduction

This library eases the use of the Twitch Alerts API. The current version is built to work with the v1.0 of Twitch Alerts API.

## How to use

You first have to register your application with Twitch Alerts, to do this, go on this URL : 

[--> Register your application](http://www.twitchalerts.com/oauth/apps/register)

Then you can use the API Wrapper knowing your client_id, client_secret and redirect_uri parameters.

Twitch API uses OAuth 2.0 to handle user authentication. This library helps you to handle the authentication process as well.

### User Authentication

You will first need to instanciate a Client :

```php
<?php

$client = new Client($clientId, $clientSecret, $redirectUri); // $redirectUri should also match exactly the data you entered when you registred your application
```

You will need to redirect the user to Twitch Alerts when he will accept (or deny) your application to acces his account.
You can ask for multiples scopes. Further you will be limited by the scopes you asked on this step. (The list of the available scopes are available in ```Client::SCOPES ```.

```php
<?php

$client = new Client($clientId, $clientSecret, $redirectUri);
$redirectUrl = $client->getAuthorizeUrl(['donations.create']); // You can pass multiple scopes here

// You should then need to redirect the user to this URI.
// When the user will return to your redirect_uri either the "code" parameter will be present on the query parameters (you will need it for the next step) or an "error" parameter will be present which means the user refused to allow your application to access his account.
```

When you have the "code" parameter returned from the previous step will be able to ask for an access_token and a refresh_token. The access_token is needed for any further call you make to the API and the refresh_token is needed to refresh your access_token when it will expire.

To generate your first access_token and refresh_token :

```php
<?php
$client = new Client($clientId, $clientSecret, $redirectUri);
$tokens = $client->getAccessToken($code); // $code is the code returned on the previous step

// $tokens will contains multiple keys : "access_token" and "refresh_token".
````

You can now use other API methods for 3600 seconds, then you will need to refresh your access token :

```php
<?php
$client = new Client($clientId, $clientSecret, $redirectUri);
$tokens = $client->refreshAccessToken($refreshToken); // $refreshToken is the refreshToken you got from the previous step (or the last refreshAccessToken you did).

// $tokens will contains multiple keys : "access_token" and "refresh_token".
````
