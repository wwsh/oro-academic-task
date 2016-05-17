<?php
/*******************************************************************************
 * This is closed source software, created by WWSH.
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016.
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Migrations\Data\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Oro\Bundle\SecurityBundle\Acl\Persistence\AclManager;
use Oro\Bundle\UserBundle\Entity\Role;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UpdateIssueAccessLevels extends AbstractFixture implements
    DependentFixtureInterface,
    ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @return int
     */
    public function getOrder()
    {
        return 5;
    }

    /**
     * @return array
     */
    public function getDependencies()
    {
        return [ 'Oro\Bundle\SecurityBundle\Migrations\Data\ORM\LoadAclRoles' ];
    }

    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $this->objectManager = $manager;

        /** @var AclManager $aclManager */
        $aclManager = $this->container->get('oro_security.acl.manager');

        if ($aclManager->isAclEnabled()) {
            $this->updateUserRole($aclManager);
            $aclManager->flush();
        }
    }

    /**
     * @param AclManager $manager
     */
    protected function updateUserRole(AclManager $manager)
    {
        $roles = [
            'ROLE_USER',
            'ROLE_MANAGER',
        ];

        foreach ($roles as $roleName) {
            $role = $this->getRole($roleName);
            if ($role) {
                $sid = $manager->getSid($role);

                $oid         = $manager->getOid('entity:OroAcademy\Bundle\IssueBundle\Entity\Issue');
                $maskBuilder = $manager->getMaskBuilder($oid)
                                       ->add('VIEW_SYSTEM')
                                       ->add('CREATE_SYSTEM')
                                       ->add('EDIT_SYSTEM');
                $manager->setPermission($sid, $oid, $maskBuilder->get());
            }
        }
    }

    /**
     * @param string $roleName
     * @return Role|null
     */
    protected function getRole($roleName)
    {
        return $this->objectManager
            ->getRepository('OroUserBundle:Role')
            ->findOneBy([ 'role' => $roleName ]);
    }
}