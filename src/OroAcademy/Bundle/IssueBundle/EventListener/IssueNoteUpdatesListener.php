<?php
/*******************************************************************************
 * This is closed source software, created by WWSH. 
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016. 
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\EventListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Oro\Bundle\NoteBundle\Entity\Note;
use OroAcademy\Bundle\IssueBundle\Entity\Issue;

/**
 * The purpose of this listener is to listen to incoming note updates
 * and update updatedAt field in the related issue entity.
 * @package OroAcademy\Bundle\IssueBundle\EventListener
 */
class IssueNoteUpdatesListener
{
    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $manager = $args->getObjectManager();

        if ($args->getObject() instanceof Note) {
            /** @var Note $note */
            $note = $args->getObject();
            /** @var Issue $issue */
            $issue = $note->getTarget();
            
            if ($issue instanceof Issue) {
                $issue->setUpdatedAt(new \DateTime());
                $manager->persist($issue);
                $manager->flush(); // there will be no deadlock, since it's another entity
            }
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->postPersist($args);
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        $this->postPersist($args);
    }
}