<?php

namespace StripeWPFS\Exception\OAuth;

/**
 * InvalidRequestException is thrown when a code, refresh token, or grant
 * type parameter is not provided, but was required.
 *
 * @package StripeWPFS\Exception\OAuth
 */
class InvalidRequestException extends OAuthErrorException
{
}
