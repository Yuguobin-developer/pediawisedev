<?php

namespace StripeWPFS;

/**
 * Class UsageRecord
 *
 * @package StripeWPFS
 *
 * @property string $id
 * @property string $object
 * @property string $invoice
 * @property bool $livemode
 * @property \StripeWPFS\StripeObject $period
 * @property string $subscription_item
 * @property int $total_usage
 */
class UsageRecordSummary extends ApiResource
{
    const OBJECT_NAME = 'usage_record_summary';
}
