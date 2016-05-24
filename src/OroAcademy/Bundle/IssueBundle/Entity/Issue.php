<?php
/*******************************************************************************
 * This is closed source software, created by WWSH.
 * Please do not copy nor redistribute.
 * Copyright (c) Oro 2016.
 ******************************************************************************/

namespace OroAcademy\Bundle\IssueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\ConfigField;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\UserBundle\Entity\User;
use Oro\Bundle\WorkflowBundle\Entity\WorkflowItem;
use Oro\Bundle\WorkflowBundle\Entity\WorkflowStep;
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
 * @Config(
 *     defaultValues={
 *      "workflow"={
 *          "active_workflow"="issue_flow",
 *          "show_step_in_grid"=false
 *      },
 *     "ownership"={
 *          "owner_type"="USER",
 *          "owner_field_name"="reporter",
 *          "owner_column_name"="reporter_id",
 *          "organization_field_name"="organization",
 *          "organization_column_name"="organization_id"
 *      },
 *      "security"={
 *          "type"="ACL",
 *          "permissions"="All"
 *      },
 *       "tag"={
 *          "enabled"=true
 *      }
 *    }
 * )
 */
class Issue extends ExtendIssue
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "excluded"=true
     *          }
     *      }
     * )
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="summary", type="string", length=255, nullable=true)
     *
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "order"=20
     *          }
     *      }
     * )
     */
    protected $summary;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, nullable=true)
     *
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "order"=10,
     *              "identity"=true
     *          }
     *      }
     * )
     */
    protected $code;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     *
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "order"=30
     *          }
     *      }
     * )
     */
    protected $description;

    /**
     * @var IssueType
     *
     * @ORM\ManyToOne(targetEntity="OroAcademy\Bundle\IssueBundle\Entity\IssueType")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     *
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "order"=40
     *          }
     *      }
     * )
     */
    protected $type;

    /**
     * @var IssuePriority
     *
     * @ORM\ManyToOne(targetEntity="OroAcademy\Bundle\IssueBundle\Entity\IssuePriority")
     * @ORM\JoinColumn(name="priority_id", referencedColumnName="id")
     *
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "order"=50
     *          }
     *      }
     * )
     */
    protected $priority;

    /**
     * @var IssueResolution
     *
     * @ORM\ManyToOne(targetEntity="OroAcademy\Bundle\IssueBundle\Entity\IssueResolution")
     * @ORM\JoinColumn(name="resolution_id", referencedColumnName="id")
     *
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "order"=60
     *          }
     *      }
     * )
     */
    protected $resolution = null;


    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="reporter_user_id", referencedColumnName="id", onDelete="SET NULL")
     *
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "order"=80
     *          }
     *      }
     * )
     */
    protected $reporter;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="assignee_user_id", referencedColumnName="id", onDelete="SET NULL")
     *
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "order"=90
     *          }
     *      }
     * )
     */
    protected $assignee;

    /**
     * @var Issue[]
     *
     * @ORM\ManyToMany(targetEntity="OroAcademy\Bundle\IssueBundle\Entity\Issue")
     * @ORM\JoinTable(name="oroacademy_issue_to_issue",
     *      joinColumns={@ORM\JoinColumn(name="issue_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="related_issue_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
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
     * @ORM\ManyToOne(targetEntity="OroAcademy\Bundle\IssueBundle\Entity\Issue", inversedBy="children")
     *
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "order"=100
     *          }
     *      }
     * )
     */
    protected $parent;

    /**
     * @var Issue[]
     *
     * @ORM\OneToMany(targetEntity="OroAcademy\Bundle\IssueBundle\Entity\Issue", mappedBy="parent")
     *
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "order"=110
     *          }
     *      }
     * )
     */
    protected $children;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     *
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "order"=120
     *          }
     *      }
     * )
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime", nullable=true)
     *
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "order"=130
     *          }
     *      }
     * )
     */
    protected $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\WorkflowBundle\Entity\WorkflowItem")
     * @ORM\JoinColumn(name="workflow_item_id", referencedColumnName="id", onDelete="SET NULL")
     *
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "excluded"=true
     *          }
     *      }
     * )
     */
    protected $workflowItem;

    /**
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\WorkflowBundle\Entity\WorkflowStep")
     * @ORM\JoinColumn(name="workflow_step_id", referencedColumnName="id", onDelete="SET NULL")
     *
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "excluded"=true
     *          }
     *      }
     * )
     */
    protected $workflowStep;

    /**
     * @var Organization
     *
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\OrganizationBundle\Entity\Organization")
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $organization;

    /**
     * Constructor
     */
    public function __construct($code = null, $summary = null)
    {
        $this->collaborators = new \Doctrine\Common\Collections\ArrayCollection();
        $this->relatedIssues = new \Doctrine\Common\Collections\ArrayCollection();
        $this->children      = new \Doctrine\Common\Collections\ArrayCollection();

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
     * Set reporter
     *
     * @param string $reporter
     *
     * @return Issue
     */
    public function setReporter($reporter)
    {
        $this->reporter = $reporter;

        if (null !== $reporter) {
            $this->addCollaborator($reporter);
        }

        return $this;
    }

    /**
     * Get reporter
     *
     * @return User
     */
    public function getReporter()
    {
        return $this->reporter;
    }

    /**
     * Set assignee
     *
     * @param User $assignee
     *
     * @return Issue
     */
    public function setAssignee($assignee)
    {
        $this->assignee = $assignee;

        if (null !== $assignee) {
            $this->addCollaborator($assignee);
        }

        return $this;
    }

    /**
     * Get assignee
     *
     * @return User
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
     * @param Issue $children
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
        $key = array_search($collaborator, $this->collaborators->toArray(), true);

        if (false !== $key) {
            return $this; // collab already added
        }

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
     * @return mixed
     */
    public function getWorkflowItem()
    {
        return $this->workflowItem;
    }

    /**
     * @param WorkflowItem $workflowItem
     */
    public function setWorkflowItem(WorkflowItem $workflowItem)
    {
        $this->workflowItem = $workflowItem;
    }

    /**
     * @return mixed
     */
    public function getWorkflowStep()
    {
        return $this->workflowStep;
    }

    /**
     * @param WorkflowStep $workflowStep
     */
    public function setWorkflowStep(WorkflowStep $workflowStep)
    {
        $this->workflowStep = $workflowStep;
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
     * @return string
     */
    public function getSimpleCollaboratorArray()
    {
        if (empty($this->collaborators)) {
            return [ ];
        }

        $collab = [ ];

        foreach ($this->collaborators as $collaborator) {
            $collab[] = $collaborator->getFullName();
        }

        return $collab;
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

    /**
     * Set organization
     *
     * @param \Oro\Bundle\OrganizationBundle\Entity\Organization $organization
     *
     * @return Issue
     */
    public function setOrganization(\Oro\Bundle\OrganizationBundle\Entity\Organization $organization = null)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * Get organization
     *
     * @return \Oro\Bundle\OrganizationBundle\Entity\Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @return User
     */
    public function getOwner()
    {
        return $this->getReporter();
    }

    /**
     * @param User $owner
     * @return $this
     */
    public function setOwner($owner)
    {
        return $this->setReporter($owner);
    }
}
