<?php

namespace App\Tests\Unit\Services;

use App\ApiResource\CustomDto;
use App\Services\CustomSerializationHelper;
use PHPUnit\Framework\TestCase;

class CustomSerializationHelperTest extends TestCase
{
    public function testOne()
    {
        $customSerializationHelper = new CustomSerializationHelper();
        $dto = new CustomDto();
        $number = 1;

        $dto->setNumber($number);
        $dto = $customSerializationHelper->doIncrement($dto, 1);

        $this->assertEquals($number + 1, $dto->getNumberIncreased());
    }
}