<?php
/*******************************************************************************
 * This is closed source software, created by WWSH.
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016.
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Oro\Bundle\TagBundle\Entity\Tag;
use Oro\Bundle\UserBundle\Entity\User;
use OroAcademy\Bundle\IssueBundle\Model\ExtendIssue;

/**
 * Issue
 *
 * @ORM\Table(
 *      name="oroacademy_issue"
 * )
 * @ORM\Entity(repositoryClass="OroAcademy\Bundle\IssueBundle\Entity\IssueRepository")
 *
 * @ORM\HasLifecycleCallbacks()
 *
 * @Config
 */
class Issue extends ExtendIssue
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="summary", type="string", length=255, nullable=true)
     */
    protected $summary;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, nullable=true)
     */
    protected $code;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var IssueType
     *
     * @ORM\ManyToOne(targetEntity="OroAcademy\Bundle\IssueBundle\Entity\IssueType")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     */
    protected $type;

    /**
     * @var IssuePriority
     *
     * @ORM\ManyToOne(targetEntity="OroAcademy\Bundle\IssueBundle\Entity\IssuePriority")
     * @ORM\JoinColumn(name="priority_id", referencedColumnName="id")
     */
    protected $priority;

    /**
     * @var IssueResolution
     *
     * @ORM\ManyToOne(targetEntity="OroAcademy\Bundle\IssueBundle\Entity\IssueResolution")
     * @ORM\JoinColumn(name="resolution_id", referencedColumnName="id")
     */
    protected $resolution = null;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255, nullable=true)
     */
    protected $status;

    /**
     * @var Tag[]
     *
     * @ORM\ManyToMany(targetEntity="Oro\Bundle\TagBundle\Entity\Tag")
     * @ORM\JoinTable(name="oroacademy_issue_to_tag",
     *      joinColumns={@ORM\JoinColumn(name="issue_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $tags;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="reporter_user_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $reporter;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="assignee_user_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $assignee;

    /**
     * @var Issue[]
     *
     * @ORM\OneToMany(targetEntity="OroAcademy\Bundle\IssueBundle\Entity\Issue", mappedBy="issue")
     */
    protected $relatedIssues;

    /**
     * @var User[]
     *
     * @ORM\ManyToMany(targetEntity="Oro\Bundle\UserBundle\Entity\User")
     * @ORM\JoinTable(name="oroacademy_issue_to_user",
     *      joinColumns={@ORM\JoinColumn(name="issue_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $collaborators;

    /**
     * @var Issue
     *
     * @ORM\ManyToOne(targetEntity="OroAcademy\Bundle\IssueBundle\Entity\Issue",inversedBy="children")
     */
    protected $parent;

    /**
     * @var Issue[]
     *
     * @ORM\OneToMany(targetEntity="OroAcademy\Bundle\IssueBundle\Entity\Issue", mappedBy="issue")
     */
    protected $children;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime", nullable=true)
     */
    protected $updatedAt;

    /**
     * System flag. True = we're in the cycle of generating the .code value
     *
     * @var bool
     */
    protected $generatingCode = false;

    /**
     * Constructor
     */
    public function __construct($code = null, $summary = null)
    {
        $this->collaborators = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tags          = new \Doctrine\Common\Collections\ArrayCollection();
        $this->relatedIssues = new \Doctrine\Common\Collections\ArrayCollection();

        $this->code    = $code;
        $this->summary = $summary;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set summary
     *
     * @param string $summary
     *
     * @return Issue
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * Get summary
     *
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Issue
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Issue
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Issue
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return IssueType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set priority
     *
     * @param IssuePriority $priority
     *
     * @return Issue
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return IssuePriority
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set resolution
     *
     * @param IssueRepository $resolution
     *
     * @return Issue
     */
    public function setResolution($resolution)
    {
        $this->resolution = $resolution;

        return $this;
    }

    /**
     * Get resolution
     *
     * @return IssueResolution
     */
    public function getResolution()
    {
        return $this->resolution;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Issue
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set tags
     *
     * @param string $tags
     *
     * @return Issue
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get tags
     *
     * @return string
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set reporter
     *
     * @param string $reporter
     *
     * @return Issue
     */
    public function setReporter($reporter)
    {
        $this->reporter = $reporter;

        return $this;
    }

    /**
     * Get reporter
     *
     * @return string
     */
    public function getReporter()
    {
        return $this->reporter;
    }

    /**
     * Set assignee
     *
     * @param string $assignee
     *
     * @return Issue
     */
    public function setAssignee($assignee)
    {
        $this->assignee = $assignee;

        return $this;
    }

    /**
     * Get assignee
     *
     * @return string
     */
    public function getAssignee()
    {
        return $this->assignee;
    }

    /**
     * Set relatedIssues
     *
     * @param string $relatedIssues
     *
     * @return Issue
     */
    public function setRelatedIssues($relatedIssues)
    {
        $this->relatedIssues = $relatedIssues;

        return $this;
    }

    /**
     * Get relatedIssues
     *
     * @return string
     */
    public function getRelatedIssues()
    {
        return $this->relatedIssues;
    }

    /**
     * Set collaborators
     *
     * @param string $collaborators
     *
     * @return Issue
     */
    public function setCollaborators($collaborators)
    {
        $this->collaborators = $collaborators;

        return $this;
    }

    /**
     * Get collaboratos
     *
     * @return string
     */
    public function getCollaborators()
    {
        return $this->collaborators;
    }

    /**
     * Set parent
     *
     * @param string $parent
     *
     * @return Issue
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return string
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set children
     *
     * @param string $children
     *
     * @return Issue
     */
    public function setChildren($children)
    {
        $this->children = $children;

        return $this;
    }

    /**
     * Get children
     *
     * @return string
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Issue
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Issue
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Add collaborator
     *
     * @param \Oro\Bundle\UserBundle\Entity\User $collaborator
     *
     * @return Issue
     */
    public function addCollaborator(\Oro\Bundle\UserBundle\Entity\User $collaborator)
    {
        $this->collaborators[] = $collaborator;

        return $this;
    }

    /**
     * Remove collaborator
     *
     * @param \Oro\Bundle\UserBundle\Entity\User $collaborator
     */
    public function removeCollaborator(\Oro\Bundle\UserBundle\Entity\User $collaborator)
    {
        $this->collaborators->removeElement($collaborator);
    }

    /**
     * Add child
     *
     * @param \OroAcademy\Bundle\IssueBundle\Entity\Issue $child
     *
     * @return Issue
     */
    public function addChild(\OroAcademy\Bundle\IssueBundle\Entity\Issue $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param \OroAcademy\Bundle\IssueBundle\Entity\Issue $child
     */
    public function removeChild(\OroAcademy\Bundle\IssueBundle\Entity\Issue $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Add relatedIssue
     *
     * @param \OroAcademy\Bundle\IssueBundle\Entity\Issue $relatedIssue
     *
     * @return Issue
     */
    public function addRelatedIssue(\OroAcademy\Bundle\IssueBundle\Entity\Issue $relatedIssue)
    {
        $this->relatedIssues[] = $relatedIssue;

        return $this;
    }

    /**
     * Remove relatedIssue
     *
     * @param \OroAcademy\Bundle\IssueBundle\Entity\Issue $relatedIssue
     */
    public function removeRelatedIssue(\OroAcademy\Bundle\IssueBundle\Entity\Issue $relatedIssue)
    {
        $this->relatedIssues->removeElement($relatedIssue);
    }

    /**
     * Add tag
     *
     * @param \Oro\Bundle\TagBundle\Entity\Tag $tag
     *
     * @return Issue
     */
    public function addTag(\Oro\Bundle\TagBundle\Entity\Tag $tag)
    {
        $this->tags->add($tag);

        return $this;
    }

    /**
     * Remove tag
     *
     * @param \Oro\Bundle\TagBundle\Entity\Tag $tag
     */
    public function removeTag(\Oro\Bundle\TagBundle\Entity\Tag $tag)
    {
        $this->tags->removeElement($tag);
    }

    /**
     * Handles auto Issue code generation, based on type and summary.
     * Code should be unique throughout the whole database, therefore
     * the actual code on the UI is a combination of .code and .id
     *
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        $this->createdAt = new \DateTime();

        if (empty($this->code)) {
            $this->generateCode();
        }
    }

    /**
     * @ORM\PreUpdate()
     */
    public function preUpdate()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('[%s] %s', $this->code, $this->summary);
    }

    /**
     * For the templates.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->__toString();
    }

    /**
     * due to some limitations, we cannot use the postFlush event
     * to retrieve the database ID and concat.
     * the alternative solution uses system time and a random digit.
     * this way we're most likely be having an unique ID all the time
     */
    protected function generateCode()
    {
        if (is_null($this->type)) {
            $type = chr(rand(65, 65 + 26));
        } else {
            $type = $this->type->getName();
        }

        $summaryPart = $this->summary[0];

        if (!ctype_alpha($summaryPart)) {
            $summaryPart = chr(rand(65, 65 + 26));
        }

        $this->code = strtoupper($type[0] . $summaryPart);
        $this->code .= '-' . time() . rand(0, 9);
    }
}
