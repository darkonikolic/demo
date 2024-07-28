<?php

namespace App\State;


use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Purchase;
use App\Repository\ProductRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;

#[AsDecorator('api_platform.doctrine.orm.state.persist_processor')]
class PurchaseProcessor
{
    public function __construct(
        private readonly ProcessorInterface $innerProcessor,
        private readonly Security $security,
        private readonly ProductRepository $productRepository)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if ($data instanceof Purchase) {
            if ($data->getUser() === null && $this->security->getUser()) {
                $data->setUser($this->security->getUser());
            }

            if ($data->getProduct() === null && $data->getProvidedSku() !== null) {
                $product = $this->productRepository->findOneBy(['sku' => $data->getProvidedSku()]);
                $data->setProduct($product);
            }
        }

        $this->innerProcessor->process($data, $operation, $uriVariables, $context);
    }
}
