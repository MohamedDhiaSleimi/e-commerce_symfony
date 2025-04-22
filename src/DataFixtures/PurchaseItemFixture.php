<?php

namespace App\DataFixtures;

use App\Entity\PurchaseItem;
use App\Entity\Purchase;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class PurchaseItemFixture extends Fixture implements DependentFixtureInterface
{
    // All product references from ProductFixture
    private $productReferences = [];
    
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        
        // First, collect all product references
        $reflection = new \ReflectionClass(ProductFixture::class);
        $productsConstant = $reflection->getConstant('PRODUCTS');
        
        foreach ($productsConstant as $categoryKey => $products) {
            foreach ($products as $productData) {
                $this->productReferences[] = 'product_' . $this->slugify($productData['name']);
            }
        }
        
        // For each user's purchase
        for ($userId = 1; $userId <= 20; $userId++) {
            // Each user will have 0-3 orders
            $numOrders = random_int(0, 3);
            
            for ($orderId = 0; $orderId < $numOrders; $orderId++) {
                $purchase = $this->getReference('purchase_' . $userId . '_' . $orderId, Purchase::class);
                
                // Each order will have 1-5 items
                $numItems = random_int(1, 5);
                $orderTotal = 0;
                
                // Use a set to prevent duplicate products in the same order
                $usedProducts = [];
                
                for ($i = 0; $i < $numItems; $i++) {
                    // Keep trying until we get a product that's not already in this order
                    do {
                        $productReference = $faker->randomElement($this->productReferences);
                    } while (in_array($productReference, $usedProducts));
                    
                    $usedProducts[] = $productReference;
                    
                    $product = $this->getReference($productReference, Product::class);
                    $quantity = random_int(1, 3);
                    
                    $purchaseItem = new PurchaseItem();
                    $purchaseItem->setPurchase($purchase);
                    $purchaseItem->setProduct($product);
                    $purchaseItem->setQuantity($quantity);
                    $purchaseItem->setPrice($product->getPrice());
                    
                    $itemTotal = $product->getPrice() * $quantity;
                    $orderTotal += $itemTotal;
                    
                    $manager->persist($purchaseItem);
                }
                
                // Update the purchase total
                $purchase->setTotalAmount($orderTotal);
                $manager->persist($purchase);
            }
        }

        $manager->flush();
    }
    
    private function slugify(string $text): string
    {
        // Replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        // Transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        // Remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);
        // Trim
        $text = trim($text, '-');
        // Remove duplicate -
        $text = preg_replace('~-+~', '-', $text);
        // Lowercase
        $text = strtolower($text);

        return $text;
    }

    public function getDependencies(): array
    {
        return [
            \App\DataFixtures\ProductFixture::class,
            \App\DataFixtures\PurchaseFixture::class,
        ];
    }
}