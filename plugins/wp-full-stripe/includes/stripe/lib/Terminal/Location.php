<?php

namespace StripeWPFS\Terminal;

/**
 * Class Location
 *
 * @property string $id Unique identifier for the object.
 * @property string $object String representing the object's type. Objects of the same type share the same value.
 * @property \StripeWPFS\StripeObject $address
 * @property string $display_name The display name of the location.
 * @property bool $livemode Has the value <code>true</code> if the object exists in live mode or the value <code>false</code> if the object exists in test mode.
 * @property \StripeWPFS\StripeObject $metadata Set of key-value pairs that you can attach to an object. This can be useful for storing additional information about the object in a structured format.
 *
 * @package StripeWPFS\Terminal
 */
class Location extends \StripeWPFS\ApiResource
{
    const OBJECT_NAME = 'terminal.location';

    use \StripeWPFS\ApiOperations\All;
    use \StripeWPFS\ApiOperations\Create;
    use \StripeWPFS\ApiOperations\Delete;
    use \StripeWPFS\ApiOperations\Retrieve;
    use \StripeWPFS\ApiOperations\Update;
}
