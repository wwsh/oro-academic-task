<?php
/*******************************************************************************
 * This is closed source software, created by WWSH.
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016.
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Tests\Functional\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\UserBundle\Entity\UserManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadCasualUsers extends AbstractFixture implements ContainerAwareInterface
{
    /**
     * @var EntityManager
     */
    private $manager;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }


    public function load(ObjectManager $manager)
    {
        $doctrine       = $this->container->get('doctrine');
        $encoderFactory = $this->container->get('security.encoder_factory');
        $class          = 'Oro\Bundle\UserBundle\Entity\User';

        $this->userManager = new UserManager($class, $doctrine, $encoderFactory);

        $this->manager = $doctrine->getManager();

        $this->createUser('John', 'Doe');
    }

    /**
     * @param string $firstName
     * @param string $lastName
     *
     * @return User
     */
    private function createUser($firstName, $lastName)
    {
        $user = new User();
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setUsername(strtolower($firstName . '.' . $lastName));
        $user->setEmail(strtolower($firstName . '_' . $lastName . '@example.com'));
        $user->setPlainPassword(strtolower($firstName . '.' . $lastName));
        $this->userManager->updatePassword($user);

        $this->manager->persist($user);
        $this->manager->flush();

        return $user;
    }

}