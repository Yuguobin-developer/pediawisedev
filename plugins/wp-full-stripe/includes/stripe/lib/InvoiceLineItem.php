<?php

namespace StripeWPFS;

/**
 * Class InvoiceLineItem
 *
 * @property string $id
 * @property string $object
 * @property int $amount
 * @property string $currency
 * @property string $description
 * @property bool $discountable
 * @property string $invoice_item
 * @property bool $livemode
 * @property \StripeWPFS\StripeObject $metadata
 * @property \StripeWPFS\StripeObject $period
 * @property \StripeWPFS\Plan $plan
 * @property bool $proration
 * @property int $quantity
 * @property string $subscription
 * @property string $subscription_item
 * @property array $tax_amounts
 * @property array $tax_rates
 * @property string $type
 *
 * @package StripeWPFS
 */
class InvoiceLineItem extends ApiResource
{
    const OBJECT_NAME = 'line_item';
}
