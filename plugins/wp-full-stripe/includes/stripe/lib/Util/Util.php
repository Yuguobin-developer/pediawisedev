<?php

namespace StripeWPFS\Util;

use StripeWPFS\StripeObject;

abstract class Util
{
    private static $isMbstringAvailable = null;
    private static $isHashEqualsAvailable = null;

    /**
     * Whether the provided array (or other) is a list rather than a dictionary.
     * A list is defined as an array for which all the keys are consecutive
     * integers starting at 0. Empty arrays are considered to be lists.
     *
     * @param array|mixed $array
     *
     * @return bool true if the given object is a list.
     */
    public static function isList($array)
    {
        if (!\is_array($array)) {
            return false;
        }
        if ($array === []) {
            return true;
        }
        if (\array_keys($array) !== \range(0, \count($array) - 1)) {
            return false;
        }
        return true;
    }

    /**
     * Converts a response from the Stripe API to the corresponding PHP object.
     *
     * @param array $resp The response from the Stripe API.
     * @param array $opts
     *
     * @return array|StripeObject
     */
    public static function convertToStripeObject($resp, $opts)
    {
        $types = [
            // data structures
            \StripeWPFS\Collection::OBJECT_NAME => \StripeWPFS\Collection::class,

            // business objects
            \StripeWPFS\Account::OBJECT_NAME => \StripeWPFS\Account::class,
            \StripeWPFS\AccountLink::OBJECT_NAME => \StripeWPFS\AccountLink::class,
            \StripeWPFS\AlipayAccount::OBJECT_NAME => \StripeWPFS\AlipayAccount::class,
            \StripeWPFS\ApplePayDomain::OBJECT_NAME => \StripeWPFS\ApplePayDomain::class,
            \StripeWPFS\ApplicationFee::OBJECT_NAME => \StripeWPFS\ApplicationFee::class,
            \StripeWPFS\ApplicationFeeRefund::OBJECT_NAME => \StripeWPFS\ApplicationFeeRefund::class,
            \StripeWPFS\Balance::OBJECT_NAME => \StripeWPFS\Balance::class,
            \StripeWPFS\BalanceTransaction::OBJECT_NAME => \StripeWPFS\BalanceTransaction::class,
            \StripeWPFS\BankAccount::OBJECT_NAME => \StripeWPFS\BankAccount::class,
            \StripeWPFS\BitcoinReceiver::OBJECT_NAME => \StripeWPFS\BitcoinReceiver::class,
            \StripeWPFS\BitcoinTransaction::OBJECT_NAME => \StripeWPFS\BitcoinTransaction::class,
            \StripeWPFS\Capability::OBJECT_NAME => \StripeWPFS\Capability::class,
            \StripeWPFS\Card::OBJECT_NAME => \StripeWPFS\Card::class,
            \StripeWPFS\Charge::OBJECT_NAME => \StripeWPFS\Charge::class,
            \StripeWPFS\Checkout\Session::OBJECT_NAME => \StripeWPFS\Checkout\Session::class,
            \StripeWPFS\CountrySpec::OBJECT_NAME => \StripeWPFS\CountrySpec::class,
            \StripeWPFS\Coupon::OBJECT_NAME => \StripeWPFS\Coupon::class,
            \StripeWPFS\CreditNote::OBJECT_NAME => \StripeWPFS\CreditNote::class,
            \StripeWPFS\CreditNoteLineItem::OBJECT_NAME => \StripeWPFS\CreditNoteLineItem::class,
            \StripeWPFS\Customer::OBJECT_NAME => \StripeWPFS\Customer::class,
            \StripeWPFS\CustomerBalanceTransaction::OBJECT_NAME => \StripeWPFS\CustomerBalanceTransaction::class,
            \StripeWPFS\Discount::OBJECT_NAME => \StripeWPFS\Discount::class,
            \StripeWPFS\Dispute::OBJECT_NAME => \StripeWPFS\Dispute::class,
            \StripeWPFS\EphemeralKey::OBJECT_NAME => \StripeWPFS\EphemeralKey::class,
            \StripeWPFS\Event::OBJECT_NAME => \StripeWPFS\Event::class,
            \StripeWPFS\ExchangeRate::OBJECT_NAME => \StripeWPFS\ExchangeRate::class,
            \StripeWPFS\File::OBJECT_NAME => \StripeWPFS\File::class,
            \StripeWPFS\File::OBJECT_NAME_ALT => \StripeWPFS\File::class,
            \StripeWPFS\FileLink::OBJECT_NAME => \StripeWPFS\FileLink::class,
            \StripeWPFS\Invoice::OBJECT_NAME => \StripeWPFS\Invoice::class,
            \StripeWPFS\InvoiceItem::OBJECT_NAME => \StripeWPFS\InvoiceItem::class,
            \StripeWPFS\InvoiceLineItem::OBJECT_NAME => \StripeWPFS\InvoiceLineItem::class,
            \StripeWPFS\Issuing\Authorization::OBJECT_NAME => \StripeWPFS\Issuing\Authorization::class,
            \StripeWPFS\Issuing\Card::OBJECT_NAME => \StripeWPFS\Issuing\Card::class,
            \StripeWPFS\Issuing\CardDetails::OBJECT_NAME => \StripeWPFS\Issuing\CardDetails::class,
            \StripeWPFS\Issuing\Cardholder::OBJECT_NAME => \StripeWPFS\Issuing\Cardholder::class,
            \StripeWPFS\Issuing\Dispute::OBJECT_NAME => \StripeWPFS\Issuing\Dispute::class,
            \StripeWPFS\Issuing\Transaction::OBJECT_NAME => \StripeWPFS\Issuing\Transaction::class,
            \StripeWPFS\LoginLink::OBJECT_NAME => \StripeWPFS\LoginLink::class,
            \StripeWPFS\Mandate::OBJECT_NAME => \StripeWPFS\Mandate::class,
            \StripeWPFS\Order::OBJECT_NAME => \StripeWPFS\Order::class,
            \StripeWPFS\OrderItem::OBJECT_NAME => \StripeWPFS\OrderItem::class,
            \StripeWPFS\OrderReturn::OBJECT_NAME => \StripeWPFS\OrderReturn::class,
            \StripeWPFS\PaymentIntent::OBJECT_NAME => \StripeWPFS\PaymentIntent::class,
            \StripeWPFS\PaymentMethod::OBJECT_NAME => \StripeWPFS\PaymentMethod::class,
            \StripeWPFS\Payout::OBJECT_NAME => \StripeWPFS\Payout::class,
            \StripeWPFS\Person::OBJECT_NAME => \StripeWPFS\Person::class,
            \StripeWPFS\Plan::OBJECT_NAME => \StripeWPFS\Plan::class,
            \StripeWPFS\Product::OBJECT_NAME => \StripeWPFS\Product::class,
            \StripeWPFS\Radar\EarlyFraudWarning::OBJECT_NAME => \StripeWPFS\Radar\EarlyFraudWarning::class,
            \StripeWPFS\Radar\ValueList::OBJECT_NAME => \StripeWPFS\Radar\ValueList::class,
            \StripeWPFS\Radar\ValueListItem::OBJECT_NAME => \StripeWPFS\Radar\ValueListItem::class,
            \StripeWPFS\Recipient::OBJECT_NAME => \StripeWPFS\Recipient::class,
            \StripeWPFS\RecipientTransfer::OBJECT_NAME => \StripeWPFS\RecipientTransfer::class,
            \StripeWPFS\Refund::OBJECT_NAME => \StripeWPFS\Refund::class,
            \StripeWPFS\Reporting\ReportRun::OBJECT_NAME => \StripeWPFS\Reporting\ReportRun::class,
            \StripeWPFS\Reporting\ReportType::OBJECT_NAME => \StripeWPFS\Reporting\ReportType::class,
            \StripeWPFS\Review::OBJECT_NAME => \StripeWPFS\Review::class,
            \StripeWPFS\SetupIntent::OBJECT_NAME => \StripeWPFS\SetupIntent::class,
            \StripeWPFS\Sigma\ScheduledQueryRun::OBJECT_NAME => \StripeWPFS\Sigma\ScheduledQueryRun::class,
            \StripeWPFS\SKU::OBJECT_NAME => \StripeWPFS\SKU::class,
            \StripeWPFS\Source::OBJECT_NAME => \StripeWPFS\Source::class,
            \StripeWPFS\SourceTransaction::OBJECT_NAME => \StripeWPFS\SourceTransaction::class,
            \StripeWPFS\Subscription::OBJECT_NAME => \StripeWPFS\Subscription::class,
            \StripeWPFS\SubscriptionItem::OBJECT_NAME => \StripeWPFS\SubscriptionItem::class,
            \StripeWPFS\SubscriptionSchedule::OBJECT_NAME => \StripeWPFS\SubscriptionSchedule::class,
            \StripeWPFS\TaxId::OBJECT_NAME => \StripeWPFS\TaxId::class,
            \StripeWPFS\TaxRate::OBJECT_NAME => \StripeWPFS\TaxRate::class,
            \StripeWPFS\ThreeDSecure::OBJECT_NAME => \StripeWPFS\ThreeDSecure::class,
            \StripeWPFS\Terminal\ConnectionToken::OBJECT_NAME => \StripeWPFS\Terminal\ConnectionToken::class,
            \StripeWPFS\Terminal\Location::OBJECT_NAME => \StripeWPFS\Terminal\Location::class,
            \StripeWPFS\Terminal\Reader::OBJECT_NAME => \StripeWPFS\Terminal\Reader::class,
            \StripeWPFS\Token::OBJECT_NAME => \StripeWPFS\Token::class,
            \StripeWPFS\Topup::OBJECT_NAME => \StripeWPFS\Topup::class,
            \StripeWPFS\Transfer::OBJECT_NAME => \StripeWPFS\Transfer::class,
            \StripeWPFS\TransferReversal::OBJECT_NAME => \StripeWPFS\TransferReversal::class,
            \StripeWPFS\UsageRecord::OBJECT_NAME => \StripeWPFS\UsageRecord::class,
            \StripeWPFS\UsageRecordSummary::OBJECT_NAME => \StripeWPFS\UsageRecordSummary::class,
            \StripeWPFS\WebhookEndpoint::OBJECT_NAME => \StripeWPFS\WebhookEndpoint::class,
        ];
        if (self::isList($resp)) {
            $mapped = [];
            foreach ($resp as $i) {
                \array_push($mapped, self::convertToStripeObject($i, $opts));
            }
            return $mapped;
        }
        if (\is_array($resp)) {
            if (isset($resp['object']) && \is_string($resp['object']) && isset($types[$resp['object']])) {
                $class = $types[$resp['object']];
            } else {
                $class = \StripeWPFS\StripeObject::class;
            }
            return $class::constructFrom($resp, $opts);
        }
        return $resp;
    }

    /**
     * @param mixed|string $value A string to UTF8-encode.
     *
     * @return mixed|string The UTF8-encoded string, or the object passed in if
     *    it wasn't a string.
     */
    public static function utf8($value)
    {
        if (null === self::$isMbstringAvailable) {
            self::$isMbstringAvailable = \function_exists('mb_detect_encoding');

            if (!self::$isMbstringAvailable) {
                \trigger_error("It looks like the mbstring extension is not enabled. " .
                    "UTF-8 strings will not properly be encoded. Ask your system " .
                    "administrator to enable the mbstring extension, or write to " .
                    "support@stripe.com if you have any questions.", \E_USER_WARNING);
            }
        }

        if (\is_string($value) && self::$isMbstringAvailable && "UTF-8" !== \mb_detect_encoding($value, "UTF-8", true)) {
            return \utf8_encode($value);
        }
        return $value;
    }

    /**
     * Compares two strings for equality. The time taken is independent of the
     * number of characters that match.
     *
     * @param string $a one of the strings to compare.
     * @param string $b the other string to compare.
     *
     * @return bool true if the strings are equal, false otherwise.
     */
    public static function secureCompare($a, $b)
    {
        if (null === self::$isHashEqualsAvailable) {
            self::$isHashEqualsAvailable = \function_exists('hash_equals');
        }

        if (self::$isHashEqualsAvailable) {
            return \hash_equals($a, $b);
        }
        if (\strlen($a) !== \strlen($b)) {
            return false;
        }

        $result = 0;
        for ($i = 0; $i < \strlen($a); ++$i) {
            $result |= \ord($a[$i]) ^ \ord($b[$i]);
        }
        return 0 === $result;
    }

    /**
     * Recursively goes through an array of parameters. If a parameter is an instance of
     * ApiResource, then it is replaced by the resource's ID.
     * Also clears out null values.
     *
     * @param mixed $h
     *
     * @return mixed
     */
    public static function objectsToIds($h)
    {
        if ($h instanceof \StripeWPFS\ApiResource) {
            return $h->id;
        }
        if (static::isList($h)) {
            $results = [];
            foreach ($h as $v) {
                \array_push($results, static::objectsToIds($v));
            }
            return $results;
        }
        if (\is_array($h)) {
            $results = [];
            foreach ($h as $k => $v) {
                if (null === $v) {
                    continue;
                }
                $results[$k] = static::objectsToIds($v);
            }
            return $results;
        }
        return $h;
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public static function encodeParameters($params)
    {
        $flattenedParams = self::flattenParams($params);
        $pieces = [];
        foreach ($flattenedParams as $param) {
            list($k, $v) = $param;
            \array_push($pieces, self::urlEncode($k) . '=' . self::urlEncode($v));
        }
        return \implode('&', $pieces);
    }

    /**
     * @param array $params
     * @param string|null $parentKey
     *
     * @return array
     */
    public static function flattenParams($params, $parentKey = null)
    {
        $result = [];

        foreach ($params as $key => $value) {
            $calculatedKey = $parentKey ? "{$parentKey}[{$key}]" : $key;

            if (self::isList($value)) {
                $result = \array_merge($result, self::flattenParamsList($value, $calculatedKey));
            } elseif (\is_array($value)) {
                $result = \array_merge($result, self::flattenParams($value, $calculatedKey));
            } else {
                \array_push($result, [$calculatedKey, $value]);
            }
        }

        return $result;
    }

    /**
     * @param array $value
     * @param string $calculatedKey
     *
     * @return array
     */
    public static function flattenParamsList($value, $calculatedKey)
    {
        $result = [];

        foreach ($value as $i => $elem) {
            if (self::isList($elem)) {
                $result = \array_merge($result, self::flattenParamsList($elem, $calculatedKey));
            } elseif (\is_array($elem)) {
                $result = \array_merge($result, self::flattenParams($elem, "{$calculatedKey}[{$i}]"));
            } else {
                \array_push($result, ["{$calculatedKey}[{$i}]", $elem]);
            }
        }

        return $result;
    }

    /**
     * @param string $key A string to URL-encode.
     *
     * @return string The URL-encoded string.
     */
    public static function urlEncode($key)
    {
        $s = \urlencode($key);

        // Don't use strict form encoding by changing the square bracket control
        // characters back to their literals. This is fine by the server, and
        // makes these parameter strings easier to read.
        $s = \str_replace('%5B', '[', $s);
        return \str_replace('%5D', ']', $s);
    }

    public static function normalizeId($id)
    {
        if (\is_array($id)) {
            $params = $id;
            $id = $params['id'];
            unset($params['id']);
        } else {
            $params = [];
        }
        return [$id, $params];
    }

    /**
     * Returns UNIX timestamp in milliseconds
     *
     * @return int current time in millis
     */
    public static function currentTimeMillis()
    {
        return (int) \round(\microtime(true) * 1000);
    }
}
