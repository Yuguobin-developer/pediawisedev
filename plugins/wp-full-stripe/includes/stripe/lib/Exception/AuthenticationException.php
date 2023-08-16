<?php

namespace StripeWPFS\Exception;

/**
 * AuthenticationException is thrown when invalid credentials are used to
 * connect to Stripe's servers.
 *
 * @package StripeWPFS\Exception
 */
class AuthenticationException extends ApiErrorException
{
}
