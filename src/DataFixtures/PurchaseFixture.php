<?php

namespace App\DataFixtures;

use App\Entity\Purchase;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class PurchaseFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        
        $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        $paymentMethods = ['credit_card', 'paypal', 'bank_transfer', 'cash_on_delivery'];
        
        // Create purchases for regular users (not admin)
        for ($userId = 1; $userId <= 20; $userId++) {
            // Each user will have 0-3 orders
            $numOrders = 3;
            
            for ($i = 0; $i < $numOrders; $i++) {
                $purchase = new Purchase();
                $purchase->setUser($this->getReference('user_' . $userId,User::class));
                
                // Random date within the last 3 months
                $date = new \DateTimeImmutable('-' . rand(1, 90) . ' days');
                $purchase->setPurchaseDate($date);
                
                $purchase->setStatus($faker->randomElement($statuses));
                $purchase->setShippingAddress($faker->address());
                $purchase->setPayementMethode($faker->randomElement($paymentMethods));
                
                // Total amount will be calculated when we add purchase items
                $purchase->setTotalAmount(0);
                
                $manager->persist($purchase);
                
                // Store reference for purchase items
                $this->addReference('purchase_' . $userId . '_' . $i, $purchase);
            }
        }

        $manager->flush();
    }

    public function getDependencies() : array
    {
        return [
            \App\DataFixtures\UserFixture::class,
        ];
    }
}