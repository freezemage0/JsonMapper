<?php

namespace Tnapf\JsonMapper\Tests\Fakes;

use Tnapf\JsonMapper\Type\FloatType;
use Tnapf\JsonMapper\Type\SnakeToCamelCase;

#[SnakeToCamelCase]
class Address
{
    public string $street;
    public string $city;
    public string $country;
    public int|string $zip;
    public ?float $latitudeDegrees;

    #[FloatType(name: 'longitude_degrees', nullable: true)]
    public float $longitudeDegrees;
}
