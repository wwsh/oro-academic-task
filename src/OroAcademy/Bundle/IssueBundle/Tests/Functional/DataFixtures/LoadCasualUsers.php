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
use Oro\Bundle\UserBundle\Entity\UserApi;
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

        $this->createUser('Dick', 'Tracy');
    }

    /**
     * @param string $firstName
     * @param string $lastName
     *
     * @return User
     */
    private function createUser($firstName, $lastName)
    {
        /** @var UserManager $userManager */
        $userManager = $this->container->get('oro_user.manager');

        $manager = $this->manager;

        $username = strtolower($firstName . '.' . $lastName);

        $user  = $manager->getRepository('OroUserBundle:User')->findOneBy([ 'username' => $username ]);
        $group = $manager->getRepository('OroUserBundle:Group')->findOneBy([ 'name' => 'Administrators' ]);
        if (!$user) {
            $role = $manager->getRepository('OroUserBundle:Role')->findOneBy([ 'role' => 'ROLE_ADMINISTRATOR' ]);
            $user = $userManager->createUser();
            $user
                ->setUsername($username)
                ->addRole($role);
        }

        $user
            ->setPlainPassword(strtolower($firstName . '.' . $lastName))
            ->setFirstname($firstName)
            ->setLastname($lastName)
            ->setEmail(strtolower($firstName . '_' . $lastName . '@example.com'))
            ->setSalt('');

        if (0 === count($user->getApiKeys())) {
            $organization = $manager->getRepository('OroOrganizationBundle:Organization')->getFirst();
            $api          = new UserApi();
            $api->setApiKey($username . '_api_key')
                ->setUser($user)
                ->setOrganization($organization);

            $user->addOrganization($organization);
            $user->addApiKey($api);
        }

        if (!$user->hasGroup($group)) {
            $user->addGroup($group);
        }

        $userManager->updateUser($user);
    }

}