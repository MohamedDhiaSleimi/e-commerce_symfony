<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class UserFixture extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Create an admin user
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $admin->setFirstName('Admin');
        $admin->setLastName('User');
        $admin->setAddress($faker->address());
        $admin->setPhoneNumber($faker->phoneNumber());
        $admin->setCreatedAt(new \DateTimeImmutable());
        
        $manager->persist($admin);
        $this->addReference('user_admin', $admin);

        // Create regular users
        for ($i = 1; $i <= 20; $i++) {
            $user = new User();
            $user->setEmail($faker->email());
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            $user->setFirstName($faker->firstName());
            $user->setLastName($faker->lastName());
            $user->setAddress($faker->address());
            $user->setPhoneNumber($faker->phoneNumber());
            $user->setCreatedAt(new \DateTimeImmutable());
            
            $manager->persist($user);
            $this->addReference('user_' . $i, $user);
        }

        $manager->flush();
    }
}