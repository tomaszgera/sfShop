<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Category;

class LoadCategoryData extends AbstractFixture implements OrderedFixtureInterface
{
    public function getOrder()
    {
        return 1;
    }

    public function load(ObjectManager $manager)
    {
        $category1 = new Category();
        $category1->setName('Dyski');
        $this->addReference('category1', $category1);
        $manager->persist($category1);

        $category2 = new Category();
        $category2->setName('Akcesoria');
        $this->addReference('category2', $category2);
        $manager->persist($category2);

        $category3 = new Category();
        $category3->setName('Peryferia');
        $this->addReference('category3', $category3);
        $manager->persist($category3);

        $manager->flush();
    }
}