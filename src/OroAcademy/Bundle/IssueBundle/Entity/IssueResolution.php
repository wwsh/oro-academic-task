<?php

namespace OroAcademy\Bundle\IssueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\ConfigField;

/**
 * IssueResolution
 *
 * @ORM\Table(
 *     name="oroacademy_issue_resolution"
 * )
 * @ORM\Entity(repositoryClass="OroAcademy\Bundle\IssueBundle\Entity\IssueResolutionRepository")
 * 
 * @Config()
 */
class IssueResolution
{
    const RESOLUTION_FIXED      = 'fixed';
    const RESOLUTION_INVALID    = 'invalid';
    const RESOLUTION_WONTFIX    = 'wontfix';
    const RESOLUTION_DUPLICATE  = 'duplicate';
    const RESOLUTION_WORKSFORME = 'worksforme';
    const RESOLUTION_INCOMPLETE = 'incomplete';

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
     * @ORM\Column(name="name", type="string", length=255)
     *
     * @ConfigField(
     *      defaultValues={
     *          "importexport"={
     *              "identity"=true
     *          }
     *      }
     * )
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255)
     */
    protected $label;

    /**
     * IssueResolution constructor.
     * @param      $name
     * @param null $label
     */
    public function __construct($name = null, $label = null)
    {
        $this->name  = $name;
        $this->label = $label;
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
     * Set name
     *
     * @param string $name
     *
     * @return IssueResolution
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return IssueResolution
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
     * @return null|string
     */
    public function __toString()
    {
        return $this->label;
    }

    /**
     * Set label
     *
     * @param string $label
     *
     * @return IssueResolution
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }
}
