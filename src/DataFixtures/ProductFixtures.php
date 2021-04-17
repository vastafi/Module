<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $string = 'ABC';
        for ($i = 0; $i < 30; $i++) {

            $product = new Product();
            $product->setCode('AB'.$i);
            $product->setName('Produs'.$i);
            $product->setCategory('Categorie'.substr(str_shuffle($string), 0, 1));
            $product->setPrice(mt_rand(100, 10000));
            $product->setDescription('Small description of the product'.$i);
            $product->setCreatedAt(new \DateTime(null, new \DateTimeZone('Europe/Athens')));
            $manager->persist($product);
        }

        $manager->flush();
    }
}
