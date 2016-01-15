<?php

namespace TwitchAlerts;

use TwitchAlerts\Exception\InvalidScopeException;
use GuzzleHttp\Client as HttpClient;

/**
 * Class Client
 *
 * @package TwitchAlerts
 */
class Client
{
    /**
     * Twitch alerts API base URI
     */
    const BASE_URI = 'https://www.twitchalerts.com';

    /**
     *  Twitch alerts Authorize endpoint path
     */
    const AUTHORIZE_ENDPOINT = '/api/v1.0/authorize';

    /**
     *  Twitch alerts Token endpoint path
     */
    const TOKEN_ENDPOINT = '/api/v1.0/token';

    /**
     *  Twitch alerts Donations endpoint path
     */
    const DONATION_ENDPOINT = '/api/v1.0/donations';

    /**
     *  Twitch alerts Alert endpoint path
     */
    const ALERT_ENDPOINT = '/api/v1.0/alerts';

    /**
     * Twitch alerts API scopes
     */
    const SCOPES = [
        'donations.create',
        'donations.read',
        'alerts.create',
    ];

    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $clientSecret;

    /**
     * @var string
     */
    protected $redirectUrl;

    /**
     * Client constructor.
     *
     * @param string $clientId
     * @param string $clientSecret
     * @param string $redirectUrl
     */
    public function __construct($clientId, $clientSecret, $redirectUrl)
    {
        $this->clientId     = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUrl  = $redirectUrl;
        $this->httpClient   = new HttpClient(['base_uri' => self::BASE_URI]);
    }

    /**
     * @param array $scopes
     *
     * @return string
     * @throws InvalidScopeException
     */
    public function getAuthorizeUrl(array $scopes = self::SCOPES)
    {
        foreach ($scopes as $scope) {
            if (!in_array($scope, self::SCOPES)) {
                throw new InvalidScopeException(sprintf('Invalid scope %s', $scope));
            }
        }

        $query = [
            'response_type' => 'code',
            'client_id'     => $this->clientId,
            'redirect_uri'  => $this->redirectUrl,
            'scope'         => implode(',', $scopes)
        ];

        return self::BASE_URI . self::AUTHORIZE_ENDPOINT . '?' . http_build_query($query);
    }

    /**
     * @param string $code
     *
     * @return array
     */
    public function getAccessToken($code)
    {
        try {
            $response = $this->httpClient->post(self::TOKEN_ENDPOINT, [
                'form_params' => [
                    'grant_type'    => 'authorization_code',
                    'client_id'     => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'redirect_uri'  => $this->redirectUrl,
                    'code'          => $code
                ]
            ]);

            var_dump((string) $response->getBody());

            return json_decode((string) $response->getBody(), true);
        } catch (\Exception $e) {
            // TODO : Throw exception
        }
    }

    /**
     * @param string $refreshToken
     *
     * @return array
     */
    public function refreshAccessToken($refreshToken)
    {
        try {
            $response = $this->httpClient->post(self::TOKEN_ENDPOINT, [
                'form_params' => [
                    'grant_type'    => 'refresh_token',
                    'client_id'     => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'redirect_uri'  => $this->redirectUrl,
                    'refresh_token' => $refreshToken
                ]
            ]);

            return json_decode((string) $response->getBody(), true);
        } catch (\Exception $e) {
            // TODO : Throw exception
        }
    }

    /**
     * @param string $accessToken
     * @param int    $limit
     * @param string $currency
     *
     * @return array
     */
    public function getDonations($accessToken, $limit, $currency)
    {
        try {
            $response = $this->httpClient->get(self::DONATION_ENDPOINT, [
                'query' => [
                    'access_token' => $accessToken,
                    'limit'        => $limit,
                    'currency'     => $currency
                ]
            ]);

            return json_decode((string) $response->getBody(), true);
        } catch (\Exception $e) {
            // TODO : Throw exception
        }
    }

    /**
     * @param string $accessToken
     * @param string $name
     * @param string $email
     * @param int    $amount
     * @param string $currency
     * @param string $message
     *
     * @return array
     */
    public function postDonation($accessToken, $name, $email, $amount, $currency, $message)
    {
        try {
            $response = $this->httpClient->post(self::DONATION_ENDPOINT, [
                'form_params' => [
                    'access_token' => $accessToken,
                    'name'         => $name,
                    'identifier'   => $email,
                    'amount'       => $amount,
                    'currency'     => $currency,
                    'message'      => $message
                ]
            ]);

            return json_decode((string) $response->getBody(), true);
        } catch (\Exception $e) {
            // TODO : Throw exception
        }
    }

    /**
     * @param string $accessToken
     * @param string $type
     * @param string $message
     * @param string $imageUrl
     * @param string $soundUrl
     * @param string $textColor
     * @param int    $duration
     *
     * @return array
     */
    public function postAlert($accessToken, $type, $message, $imageUrl, $soundUrl, $textColor, $duration)
    {
        try {
            $response = $this->httpClient->post(self::ALERT_ENDPOINT, [
                'form_params' => [
                    'access_token'       => $accessToken,
                    'type'               => $type,
                    'message'            => $message,
                    'image_href'         => $imageUrl,
                    'sound_href'         => $soundUrl,
                    'special_text_color' => $textColor,
                    'duration'           => $duration
                ]
            ]);

            return json_decode((string) $response->getBody(), true);
        } catch (\Exception $e) {
            // TODO : Throw exception
        }
    }
}
