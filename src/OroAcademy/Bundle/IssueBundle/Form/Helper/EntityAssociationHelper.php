<?php
/*******************************************************************************
 * This is closed source software, created by WWSH. 
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016. 
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Form\Helper;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * This helper converts raw input values in associated Doctrine fields
 * into their dictionary objects. The requirement is that dict
 * definitions have to be already set in the database.
 * Directly used by the REST controller in order to get rid of ID values
 * in POSTed requests, whenever there are associations.
 * This class supports MANY_TO_ONE associations only.
 */
class EntityAssociationHelper
{
    /**
     * @var Registry
     */
    private $manager;

    /**
     * @param ObjectManager $manager
     */
    public function __construct(Registry $registry)
    {
        $this->manager = $registry->getManager();
    }

    /**
     * @param $entity
     * @param $data
     * @return array
     */
    public function getEntityData($entity, $data)
    {
        if (!is_object($entity) || !is_array($data) || empty($data)) {
            return $entity;
        }

        $classMetadata = $this->manager->getClassMetadata(get_class($entity));

        foreach ($classMetadata->associationMappings as $assocField => $info) {
            // handle the case of a dict entity
            if (isset($data[$assocField])
                && $info['type'] === ClassMetadataInfo::MANY_TO_ONE
            ) {
                $targetEntityClass = $info['targetEntity'];
                $nameField         = $this->findNameFieldInEntity($targetEntityClass);
                $targetEntityRepo  = $this->manager->getRepository($targetEntityClass);
                $dictEntity        = $targetEntityRepo->findOneBy([ $nameField => $data[$assocField] ]);
                if (!empty($dictEntity)) {
                    $data[$assocField] = $dictEntity->getId();
                } else {
                    $data[$assocField] = null;
                }
            }
        }

        return $data;
    }

    /**
     * Finding probable name column, user as a dict indexer column.
     *
     * @param  $targetEntityClass
     * @return int|string
     */
    private function findNameFieldInEntity($targetEntityClass)
    {
        $classMetadata = $this->manager->getClassMetadata($targetEntityClass);

        foreach ($classMetadata->fieldMappings as $field => $mapping) {
            if ((in_array($field, [ 'name', 'code' ]) !== false
                || strpos($field, 'name') !== false)
                && 'string' === $mapping['type']
            ) {
                // let's use this as the name column
                return $field;
            }
        }

        throw new \RuntimeException('No name field mapping found - case not supported: ' . $targetEntityClass);
    }
}
