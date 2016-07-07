<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUsers implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $admin = new User();

        $admin->setUsername('admin');
        $admin->setEmail('admin@example.org');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setEnabled(true);
        $admin->setPlainPassword("123");

        $this->container->get('fos_user.user_manager')->updateUser($admin);
    }
}
