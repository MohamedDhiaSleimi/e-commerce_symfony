<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CategoryFixture extends Fixture
{
    public const CATEGORIES = [
        'CPU' => 'Central Processing Units',
        'GPU' => 'Graphics Processing Units',
        'RAM' => 'Random Access Memory',
        'SSD' => 'Solid State Drives',
        'HDD' => 'Hard Disk Drives',
        'MOBO' => 'Motherboards',
        'PSU' => 'Power Supply Units',
        'CASE' => 'Computer Cases',
        'COOLING' => 'Cooling Solutions',
        'PERIPHERAL' => 'Peripherals'
    ];

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        foreach (self::CATEGORIES as $name => $description) {
            $category = new Category();
            $category->setName($name);
            $category->setSlug(strtolower($name));
            $category->setDescription($description . '. ' . $faker->paragraph(2));
            $category->setImage('category_' . strtolower($name) . '.jpg');
            
            $manager->persist($category);
            
            // Store reference to use in other fixtures
            $this->addReference('category_' . strtolower($name), $category);
        }

        $manager->flush();
    }
}
