<?php

namespace App\Command;

use App\Entity\Product;
use App\Entity\Purchase;
use App\Entity\User;
use App\Repository\ProductRepository;
use App\Repository\PurchaseRepository;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'import:files',
    description: 'Add a short description for your command',
)]
class ImportFilesCommand extends Command
{
    public function __construct(
        private readonly UserRepository     $userRepository,
        private readonly ProductRepository  $productRepository,
        private readonly PurchaseRepository $purchaseRepository
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $userList = [];
        $productList = [];

        if (($handle = fopen('/var/www/html/src/ImportData/users.csv', "r")) !== FALSE) {
            $i = 0;
            while (($data = fgetcsv($handle, null, ",")) !== FALSE) {
                $i++;
                if ($i == 1) {
                    continue;
                }

                [$id, $name, $email, $password] = $data;

                $user = new User();
                $user->setEmail($email);
                $user->setUsername($name);
                $user->setPassword($password);

                $userList[$id] = $user;

                $this->userRepository->save($user, true);
            }
            fclose($handle);
        }

        if (($handle = fopen('/var/www/html/src/ImportData/products.csv', "r")) !== FALSE) {
            $i = 0;
            while (($data = fgetcsv($handle, null, ",")) !== FALSE) {
                $i++;
                if ($i == 1) {
                    continue;
                }

                [$sku, $name] = $data;

                $product = new Product();
                $product->setSku($sku);
                $product->setName($name);

                $productList[$sku] = $product;

                $this->productRepository->save($product, true);
            }
            fclose($handle);
        }

        if (($handle = fopen('/var/www/html/src/ImportData/purchased.csv', "r")) !== FALSE) {
            $i = 0;
            while (($data = fgetcsv($handle, null, ",")) !== FALSE) {
                $i++;
                if ($i == 1) {
                    continue;
                }

                [$userId, $sku] = $data;

                $purchase = new Purchase();
                $purchaseUser = $userList[$userId] ?? null;
                $purchaseProduct = $productList[$sku] ?? null;

                if ($purchaseUser !== null && $purchaseProduct !== null) {
                    $purchase->setUser($purchaseUser);
                    $purchase->setProduct($purchaseProduct);
                    $this->purchaseRepository->save($purchase, true);
                }
            }
            fclose($handle);
        }

        $io->success('You have a new command! ' . time());

        return Command::SUCCESS;
    }
}
