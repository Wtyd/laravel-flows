<?php

namespace Tests\Utils\CustomFaker;

use Faker\Provider\Base;
use Faker\Generator as Faker;
use Tests\Utils\CustomFaker\ExtendProviders\PhoneNumber;
use Tests\Utils\CustomFaker\Persona\PersonaProvider;

class CustomFakerProvider extends Base
{
    public static function providersRegister(Faker $faker): Faker
    {
        $faker->addProvider(new PhoneNumber($faker));
        $faker->addProvider(new PersonaProvider($faker));
        $faker->addProvider(new EnergiaProvider($faker));
        return $faker;
    }
}
