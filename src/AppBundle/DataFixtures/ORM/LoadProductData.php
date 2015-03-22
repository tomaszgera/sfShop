<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Product;

class LoadProductsData extends AbstractFixture implements OrderedFixtureInterface
{
    public function getOrder()
    {
        return 2;
    }

    public function load(ObjectManager $manager)
    {
        $product1 = new Product();
        $product1->setName('Hdd 1TB Seagate');
        $product1->setDescription('Opis dysku twardego 1TB');
        $product1->setPrice(230);
        $product1->setAmount(10);
        $product1->setCategory($this->getReference('category1'));
        $manager->persist($product1);

        $product2 = new Product();
        $product2->setName('Klawiatura multimedialna Logitech');
        $product2->setDescription('Opis klawiatury');
        $product2->setPrice(58);
        $product2->setAmount(10);
        $product2->setCategory($this->getReference('category2'));
        $manager->persist($product2);

        $product3 = new Product();
        $product3->setName('Drukarka HP LaserJet');
        $product3->setDescription('Opis drukarki');
        $product3->setPrice(390);
        $product3->setAmount(10);
        $product3->setCategory($this->getReference('category3'));
        $manager->persist($product3);

        $manager->flush();
    }

}