<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use FOS\UserBundle\Util\UserManipulator;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface {

    private $container;

    public
            function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    public function getOrder() {
        return 3;
    }

    public function load(ObjectManager $manager) {


        $faker = \Faker\Factory::create('pl_PL');
        $manipulator = $this->container->get('fos_user.util.user_manipulator');

        $i = 1;

        for ($j = 1; $j < 11; $j++) {

            
                $username = $faker->userName();
                $password = $faker->password();
                $email = $faker->email();
                $inactive = false;
                $superadmin = false;
            $user = $manipulator->create($username, $password, $email, !$inactive, $superadmin);

            $this->addReference('user' . $i++, $user);
        }

        $email = "admin@admin.pl";
        $username = "admin";
        $password = "admin";
        $inactive = false;
        $superadmin = true;

        $manipulator->create($username, $password, $email, !$inactive, $superadmin);

        $email = "user@user.pl";
        $username = "user";
        $password = "user";
        $inactive = false;
        $superadmin = false;

        $manipulator->create($username, $password, $email, !$inactive, $superadmin);


        $manager->flush();
    }

}
