<?php

namespace StripeWPFS\Exception;

/**
 * IdempotencyException is thrown in cases where an idempotency key was used
 * improperly.
 *
 * @package StripeWPFS\Exception
 */
class IdempotencyException extends ApiErrorException
{
}
