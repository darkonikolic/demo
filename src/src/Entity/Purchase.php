<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PurchaseRepository::class)]
#[ApiResource(
    uriTemplate: '/user/products',
    shortName: 'Purchases',
    operations: [
        new GetCollection(),
        new Post(
            validationContext: ['groups' => ['purchase:write:required']]
        ),
        new Delete(uriTemplate: '/user/products/{id}')
    ],
    normalizationContext: [
        'groups' => ['purchase:read']
    ],
    denormalizationContext: [
        'groups' => ['purchase:write']
    ],
    security: 'is_granted("ROLE_USER")',
)]
class Purchase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['purchase:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'purchases')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'purchases')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[Groups(['purchase:write'])]
    #[SerializedName('sku')]
    #[Assert\NotBlank(groups: ['purchase:write:required'])]
    private ?string $providedSku = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    #[SerializedName('sku')]
    #[Groups(['purchase:read'])]
    public function getSku(): string
    {
        return $this->getProduct()->getSku();
    }

    #[SerializedName('name')]
    #[Groups(['purchase:read'])]
    public function getName(): string
    {
        return $this->user->getUsername();
    }

    public function getProvidedSku(): ?string
    {
        return $this->providedSku;
    }

    public function setProvidedSku(?string $providedSku): void
    {
        $this->providedSku = $providedSku;
    }
}
