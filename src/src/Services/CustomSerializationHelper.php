<?php

namespace App\Services;

use App\ApiResource\CustomDto;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\ConstraintViolationList;
use Throwable;

class CustomSerializationHelper
{

    public function getDeserializeContext(): array
    {
        return [AbstractNormalizer::GROUPS => ['canUpdate']];
    }

    public function getSerializeContext(): array
    {
        return [AbstractNormalizer::GROUPS => 'canRead'];
    }

    public function createValidationErrorMessage(
        ConstraintViolationList $constraintViolationList,
        CustomDto               $customDto
    ): string
    {
        return 'Input not valid';
    }

    public function createRandomErrorMessage(
        Throwable $throwable,
        CustomDto $customDto
    ): string
    {
        return $throwable->getMessage();
    }

    public function doIncrement(
        CustomDto $customDto,
        int       $increment = 2
    ): CustomDto
    {
        $customDto->setNumberIncreased($customDto->getNumber() + $increment);

        return $customDto;
    }
}