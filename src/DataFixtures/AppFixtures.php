<?php

namespace App\DataFixtures;

use App\Entity\Gas\Price;
use App\Entity\Gas\Station;
use App\Entity\Gas\Type;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $types = [];
        $faker = Faker\Factory::create('fr_FR');

        for ($i=0;$i<10;$i++) {
            $entity = new Type($i, $faker->company);
            $types[] = $entity;
            $manager->persist($entity);
            $manager->flush();
        }

        for ($i=0;$i<10;$i++) {
            $entity = new Station(
                $i,
                "R",
                $faker->postcode,
                $faker->longitude,
                $faker->latitude,
                $faker->streetAddress,
                $faker->city,
                $faker->country,
                []
            );
            $manager->persist($entity);
            $manager->flush();

            for ($j=0;$j<500;$j++) {
                $price = new Price(
                    $types[rand(0,9)],
                    $entity,
                    $faker->randomFloat(3),
                    $faker->dateTime->format('Y-m-d H:i:s')
                );
                $manager->persist($price);
                $manager->flush();
            }

            dump($i);
        }
    }
}
