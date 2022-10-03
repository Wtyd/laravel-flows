<?php

declare(strict_types=1);

namespace Tests\Utils\CustomFaker\ExtendProviders;

use Faker\Provider\es_ES\PhoneNumber as FakerPhoneNumber;

class PhoneNumber extends FakerPhoneNumber
{
    protected static $customMobileFormats = [
        '## ## ####',
        '## ######',
        '########',
        '## ### ###',
        '## ## ## ##',
    ];

    protected static $prefixProbability = [
        '6', '6', '6', '7'
    ];

    public static function customMobileNumber()
    {
        return static::numerify(static::randomElement(self::$prefixProbability) . static::randomElement(static::$customMobileFormats));
    }
}
