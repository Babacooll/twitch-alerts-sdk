<?php

namespace TwitchAlerts\Exception;

use GuzzleHttp\Exception\BadResponseException;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ApiException
 *
 * @package TwitchAlerts\Exception
 */
class ApiException extends \Exception
{
    /**
     * @param BadResponseException $e
     *
     * @return ApiException
     */
    public static function fromBadResponseException(BadResponseException $e)
    {
        $message = 'An error has occurred during the API call';

        if ($e->getResponse() instanceof ResponseInterface) {
            $body = (string) $e->getResponse()->getBody();
            $json = json_decode($body, true);

            if (isset($json['message'])) {
                $message = $json['message'];
            }
        }

        return new self($message);
    }
}
