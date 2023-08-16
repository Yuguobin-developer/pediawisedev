<?php

namespace StripeWPFS;

/**
 * Class Discount
 *
 * @property string $object
 * @property Coupon $coupon
 * @property string $customer
 * @property int $end
 * @property int $start
 * @property string $subscription
 *
 * @package StripeWPFS
 */
class Discount extends StripeObject
{
    const OBJECT_NAME = 'discount';
}
