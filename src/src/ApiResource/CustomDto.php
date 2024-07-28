<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(operations: [
    new Post(
        routeName: 'book_post_publication',
        normalizationContext: [
            'groups' => ['canRead']
        ],
        denormalizationContext: [
            'groups' => ['canUpdate']
        ],
        input: CustomDto::class,
        output: CustomDto::class,
    ),
])]
class CustomDto
{
    #[Groups(['canUpdate', 'canRead'])]
    private int $number;

    #[Groups(['canRead'])]
    private int $numberIncreased;

    public function getNumber(): int
    {
        return $this->number;
    }

    public function setNumber(int $number): void
    {
        $this->number = $number;
    }

    public function getNumberIncreased(): int
    {
        return $this->numberIncreased;
    }

    public function setNumberIncreased(int $numberIncreased): void
    {
        $this->numberIncreased = $numberIncreased;
    }
}