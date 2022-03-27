<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;
use Faker\Provider\DateTime;

class UserFixture extends Fixture implements FixtureGroupInterface
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    ) {}
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        //$date = date('Y M d h:i:s');
        $date = $this->immutableDateTimeBetween();

        $admin1 = new User();
        $admin1->setCreatedAt($date);
        $admin1->setUpdatedAt($date);
        $admin1->setName($faker->lastName);
        $admin1->setEmail('admin@gmail.com');
        $admin1->setPassword($this->hasher->hashPassword($admin1,'admin'));
        $admin1->setRoles(['ROLE_ADMIN']);
        $admin2 = new User();
        $admin2->setCreatedAt($date);
        $admin2->setUpdatedAt($date);
        $admin2->setName($faker->lastName);
        $admin2->setEmail('admin2@gmail.com');
        $admin2->setPassword($this->hasher->hashPassword($admin2, 'admin'));
        $admin2->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin1);
        $manager->persist($admin2);
        for ($i=1; $i<=5;$i++) {
            $user = new User();
            $user->setName($faker->lastName);
            $user->setEmail("user$i@gmail.com");
            $user->setCreatedAt($date);
            $user->setUpdatedAt($date);
            $user->setPassword($this->hasher->hashPassword($user,'user'));
            $manager->persist($user);
        }
        $manager->flush();
    }

    public function immutableDateTimeBetween($startDate = '-30 years', $endDate = 'now', $timezone = null)
    {
        return \DateTimeImmutable::createFromMutable(
            DateTime::dateTimeBetween($startDate, $endDate, $timezone)
        );
    }

    public static function getGroups(): array
    {
        return ['user'];
    }
}