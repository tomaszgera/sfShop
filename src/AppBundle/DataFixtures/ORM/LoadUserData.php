<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    public function getOrder()
    {
        return 3;
    }

    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('pl_PL');
        
        $i=1;

        for ($j = 1; $j < 11; $j++) {
            $user = new User();
            $user->setEmail($faker->email());
            $user->setUsername($faker->userName());
            $user->setPassword($faker->password());
            $user->setEnabled(true);
            $this->addReference('user'. $i++, $user);

            $manager->persist($user);
        }
            $useradmin = new User();
            $useradmin->setEmail('admin@admin.pl');
            $useradmin->setUsername('admin');
            $useradmin->setPassword('admin');
            $useradmin->setEnabled(true);
            $useradmin->addRole('ROLE_ADMIN');
                    
            $manager->persist($useradmin);
             
            $useruser = new User();
            $useruser->setEmail('user@user.pl');
            $useruser->setUsername('user');
            $useruser->setPassword('user');
            $useruser->setEnabled(true);
            
            $manager->persist($useruser);
            
        
	$manager->flush();
    }

}