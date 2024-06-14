<?php

namespace App\DataFixtures;

use App\Entity\Order;
use App\Entity\OrderItem;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class OrderFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        /**
         * Data for orders fixtures from an array, for simple implementation.
         * Structure is: order => [product id => quantity]
         */
        $data = [
            1 => [1 => 1, 3 => 2],
            2 => [2 => 3, 3 => 1, 4 => 2],
            3 => [1 => 1, 2 => 4],
            4 => [1 => 2, 2 => 1, 4 => 1],
        ];
        foreach ($data as $orderItem => $orderData) {
            $order = new Order();
            $amount = 0;

            foreach ($orderData as $productId => $quantity) {
                $product = $this->getReference(ProductFixtures::PRODUCT_REFERENCE.$productId);

                $orderItem = new OrderItem();
                $orderItem->setProduct($product);
                $orderItem->setQuantity($quantity);

                $order->addOrderItem($orderItem);
                $amount += $product->getPrice() * $quantity;
            }

            $order->setAmount($amount);
            $manager->persist($order);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ProductFixtures::class,
        ];
    }
}
