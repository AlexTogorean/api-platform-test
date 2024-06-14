<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public const PRODUCT_REFERENCE = 'product';

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 4; $i++) {
            $product = new Product();
            $product->setName('Product '.$i);
            $product->setPrice($i * 100);
            $manager->persist($product);

            $this->addReference(self::PRODUCT_REFERENCE.$i, $product);
        }

        $manager->flush();
    }
}
