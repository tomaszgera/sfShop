<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Product;

class LoadCommentData extends AbstractFixture implements OrderedFixtureInterface
{
    public function getOrder()
    {
        return 2;
    }

    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('pl_PL');

        for ($j = 0; $j < 500; $j++) {
            $comment = new Comment();
            $comment->setContent($faker->text($maxNbChars = 200));
            $comment->setCreatedAt($faker->date($format = 'Y-m-d', $max = 'now'));
            $comment->setProduct(->getProduct());
            $comment->setUser();
            $comment->setVerified($verified);

            $manager->persist($comment);
        }
        
	$manager->flush();
    }

}