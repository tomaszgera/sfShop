<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Comment;

class LoadCommentData extends AbstractFixture implements OrderedFixtureInterface
{
    public function getOrder()
    {
        return 4;
    }

    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('pl_PL');

        for ($j = 0; $j < 500; $j++) {
            $comment = new Comment();
            $comment->setContent($faker->text($maxNbChars = 200));
            $comment->setCreatedAt($faker->dateTime($max = 'now'));
            $comment->setProduct($this->getReference('product' . $faker->numberBetween(1, 200)));
            $comment->setUser($this->getReference('user' . $faker->numberBetween(1, 10)));
            $comment->setVerified(true);

            $manager->persist($comment);
        }
        
	$manager->flush();
    }

}