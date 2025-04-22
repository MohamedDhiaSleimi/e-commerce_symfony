<?php

namespace App\DataFixtures;
use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProductFixture extends Fixture implements DependentFixtureInterface
{
    private const PRODUCTS = [
        'CPU' => [
            ['name' => 'Intel Core i9-13900K', 'brand' => 'Intel', 'price' => 599.99, 'stock' => 50],
            ['name' => 'AMD Ryzen 9 7950X', 'brand' => 'AMD', 'price' => 699.99, 'stock' => 40],
            ['name' => 'Intel Core i7-13700K', 'brand' => 'Intel', 'price' => 399.99, 'stock' => 75],
            ['name' => 'AMD Ryzen 7 7800X3D', 'brand' => 'AMD', 'price' => 449.99, 'stock' => 60],
            ['name' => 'Intel Core i5-13600K', 'brand' => 'Intel', 'price' => 319.99, 'stock' => 100],
        ],
        'GPU' => [
            ['name' => 'NVIDIA RTX 4090', 'brand' => 'NVIDIA', 'price' => 1599.99, 'stock' => 25],
            ['name' => 'AMD Radeon RX 7900 XTX', 'brand' => 'AMD', 'price' => 999.99, 'stock' => 30],
            ['name' => 'NVIDIA RTX 4080', 'brand' => 'NVIDIA', 'price' => 1199.99, 'stock' => 35],
            ['name' => 'AMD Radeon RX 7800 XT', 'brand' => 'AMD', 'price' => 599.99, 'stock' => 45],
            ['name' => 'NVIDIA RTX 4070', 'brand' => 'NVIDIA', 'price' => 599.99, 'stock' => 55],
        ],
        'RAM' => [
            ['name' => 'Corsair Vengeance 32GB DDR5', 'brand' => 'Corsair', 'price' => 159.99, 'stock' => 120],
            ['name' => 'G.Skill Trident Z5 32GB DDR5', 'brand' => 'G.Skill', 'price' => 179.99, 'stock' => 100],
            ['name' => 'Kingston Fury 16GB DDR5', 'brand' => 'Kingston', 'price' => 89.99, 'stock' => 150],
            ['name' => 'Crucial 32GB DDR4', 'brand' => 'Crucial', 'price' => 99.99, 'stock' => 80],
            ['name' => 'TeamGroup T-Force 64GB DDR5', 'brand' => 'TeamGroup', 'price' => 259.99, 'stock' => 40],
        ],
        'SSD' => [
            ['name' => 'Samsung 990 PRO 2TB NVMe', 'brand' => 'Samsung', 'price' => 199.99, 'stock' => 80],
            ['name' => 'WD Black SN850X 1TB', 'brand' => 'Western Digital', 'price' => 149.99, 'stock' => 95],
            ['name' => 'Crucial P5 Plus 2TB', 'brand' => 'Crucial', 'price' => 179.99, 'stock' => 65],
            ['name' => 'Sabrent Rocket 4 Plus 4TB', 'brand' => 'Sabrent', 'price' => 699.99, 'stock' => 30],
            ['name' => 'Kingston KC3000 2TB', 'brand' => 'Kingston', 'price' => 189.99, 'stock' => 70],
        ],
        'HDD' => [
            ['name' => 'Seagate Barracuda 4TB', 'brand' => 'Seagate', 'price' => 89.99, 'stock' => 110],
            ['name' => 'WD Blue 2TB', 'brand' => 'Western Digital', 'price' => 59.99, 'stock' => 130],
            ['name' => 'Toshiba X300 8TB', 'brand' => 'Toshiba', 'price' => 189.99, 'stock' => 45],
            ['name' => 'Seagate IronWolf 6TB NAS', 'brand' => 'Seagate', 'price' => 159.99, 'stock' => 55],
            ['name' => 'WD Red Plus 4TB NAS', 'brand' => 'Western Digital', 'price' => 119.99, 'stock' => 70],
        ],
        'MOBO' => [
            ['name' => 'ASUS ROG Maximus Z790 Hero', 'brand' => 'ASUS', 'price' => 629.99, 'stock' => 40],
            ['name' => 'MSI MPG Z790 Gaming Carbon WiFi', 'brand' => 'MSI', 'price' => 479.99, 'stock' => 55],
            ['name' => 'Gigabyte X670E AORUS Master', 'brand' => 'Gigabyte', 'price' => 499.99, 'stock' => 50],
            ['name' => 'ASRock B650 PG Riptide', 'brand' => 'ASRock', 'price' => 219.99, 'stock' => 75],
            ['name' => 'ASUS TUF Gaming B760M-PLUS', 'brand' => 'ASUS', 'price' => 179.99, 'stock' => 90],
        ],
        'PSU' => [
            ['name' => 'Corsair RM850x 80+ Gold', 'brand' => 'Corsair', 'price' => 139.99, 'stock' => 85],
            ['name' => 'EVGA SuperNOVA 1000 G6', 'brand' => 'EVGA', 'price' => 199.99, 'stock' => 60],
            ['name' => 'Seasonic FOCUS GX-750', 'brand' => 'Seasonic', 'price' => 129.99, 'stock' => 95],
            ['name' => 'be quiet! Dark Power 12 1000W', 'brand' => 'be quiet!', 'price' => 249.99, 'stock' => 35],
            ['name' => 'Thermaltake Toughpower GF3 850W', 'brand' => 'Thermaltake', 'price' => 159.99, 'stock' => 70],
        ],
        'CASE' => [
            ['name' => 'Lian Li O11 Dynamic EVO', 'brand' => 'Lian Li', 'price' => 169.99, 'stock' => 60],
            ['name' => 'Corsair 5000D Airflow', 'brand' => 'Corsair', 'price' => 174.99, 'stock' => 55],
            ['name' => 'Fractal Design Meshify 2', 'brand' => 'Fractal Design', 'price' => 159.99, 'stock' => 65],
            ['name' => 'NZXT H510 Flow', 'brand' => 'NZXT', 'price' => 89.99, 'stock' => 85],
            ['name' => 'Phanteks Eclipse P500A', 'brand' => 'Phanteks', 'price' => 149.99, 'stock' => 70],
        ],
        'COOLING' => [
            ['name' => 'NZXT Kraken X73', 'brand' => 'NZXT', 'price' => 179.99, 'stock' => 65],
            ['name' => 'Corsair iCUE H150i ELITE', 'brand' => 'Corsair', 'price' => 199.99, 'stock' => 50],
            ['name' => 'be quiet! Dark Rock Pro 4', 'brand' => 'be quiet!', 'price' => 89.99, 'stock' => 80],
            ['name' => 'Noctua NH-D15', 'brand' => 'Noctua', 'price' => 99.99, 'stock' => 70],
            ['name' => 'Arctic Liquid Freezer II 360', 'brand' => 'Arctic', 'price' => 129.99, 'stock' => 60],
        ],
        'PERIPHERAL' => [
            ['name' => 'Logitech G Pro X Superlight', 'brand' => 'Logitech', 'price' => 149.99, 'stock' => 100],
            ['name' => 'Razer BlackWidow V3 Pro', 'brand' => 'Razer', 'price' => 229.99, 'stock' => 80],
            ['name' => 'SteelSeries Arctis Nova Pro', 'brand' => 'SteelSeries', 'price' => 349.99, 'stock' => 60],
            ['name' => 'Samsung Odyssey G7 32"', 'brand' => 'Samsung', 'price' => 699.99, 'stock' => 40],
            ['name' => 'Logitech G923 Racing Wheel', 'brand' => 'Logitech', 'price' => 399.99, 'stock' => 30],
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        
        foreach (self::PRODUCTS as $categoryKey => $products) {
            $category = $this->getReference('category_' . strtolower($categoryKey),Category::class);
            
            foreach ($products as $productData) {
                $product = new Product();
                $product->setName($productData['name']);
                $product->setSlug($this->slugify($productData['name']));
                $product->setDescription($faker->paragraphs(3, true));
                $product->setPrice($productData['price']);
                $product->setStock($productData['stock']);
                $product->setCategory($category);
                $product->setBrand($productData['brand']);
                
                // Generate fake specifications based on category
                $specs = $this->generateSpecifications($categoryKey, $faker);
                $product->setSpecification($specs);
                
                $product->setImages('product_' . $this->slugify($productData['name']) . '.jpg');
                $product->setIsActive(true);
                $product->setCreatedAt(new \DateTimeImmutable());
                
                $manager->persist($product);
                
                // Store reference to use in order items
                $this->addReference('product_' . $this->slugify($productData['name']), $product);
            }
        }

        $manager->flush();
    }
    
    private function generateSpecifications(string $category, $faker): string
    {
        $specs = [];
        
        switch ($category) {
            case 'CPU':
                $specs[] = 'Cores: ' . $faker->numberBetween(4, 24);
                $specs[] = 'Threads: ' . $faker->numberBetween(8, 48);
                $specs[] = 'Base Clock: ' . $faker->numberBetween(3, 4) . '.' . $faker->numberBetween(0, 9) . ' GHz';
                $specs[] = 'Boost Clock: ' . $faker->numberBetween(4, 5) . '.' . $faker->numberBetween(0, 9) . ' GHz';
                $specs[] = 'Cache: ' . $faker->numberBetween(16, 64) . 'MB';
                $specs[] = 'TDP: ' . $faker->numberBetween(65, 125) . 'W';
                break;
            case 'GPU':
                $specs[] = 'VRAM: ' . $faker->randomElement(['8GB', '10GB', '12GB', '16GB', '24GB']) . ' GDDR6X';
                $specs[] = 'Core Clock: ' . $faker->numberBetween(1, 2) . '.' . $faker->numberBetween(0, 9) . ' GHz';
                $specs[] = 'Boost Clock: ' . $faker->numberBetween(2, 3) . '.' . $faker->numberBetween(0, 9) . ' GHz';
                $specs[] = 'CUDA Cores/Stream Processors: ' . $faker->numberBetween(5000, 18000);
                $specs[] = 'Power Connectors: ' . $faker->randomElement(['8-pin', '8-pin + 8-pin', '16-pin']);
                $specs[] = 'TDP: ' . $faker->numberBetween(200, 450) . 'W';
                break;
            case 'RAM':
                $specs[] = 'Speed: ' . $faker->randomElement(['3200MHz', '3600MHz', '4000MHz', '4800MHz', '5600MHz', '6000MHz']);
                $specs[] = 'CAS Latency: CL' . $faker->numberBetween(14, 36);
                $specs[] = 'Voltage: ' . $faker->randomElement(['1.2V', '1.35V', '1.45V']);
                $specs[] = 'Heat Spreader: ' . $faker->randomElement(['Yes', 'No']);
                $specs[] = 'RGB: ' . $faker->randomElement(['Yes', 'No']);
                break;
            default:
                $specs[] = 'Weight: ' . $faker->numberBetween(100, 2000) . 'g';
                $specs[] = 'Dimensions: ' . $faker->numberBetween(10, 50) . 'cm x ' . 
                           $faker->numberBetween(10, 50) . 'cm x ' . $faker->numberBetween(2, 20) . 'cm';
                $specs[] = 'Warranty: ' . $faker->numberBetween(1, 5) . ' years';
        }
        
        return implode("\n", $specs);
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

    public function getDependencies() : array
    {
        return [
            CategoryFixture::class,
        ];
    }
}