<?php

namespace Fsb\La13\Entity;

use DateTime;
use Serializable;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Word
 *
 * @ORM\Table(name="words")
 * @ORM\Entity(repositoryClass="Fsb\Palmeraie\Entity\WordRepository")
 * @UniqueEntity("english")
 */
class Word implements Serializable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="removed_at", type="datetime", nullable=true)
     */
    private $removedAt;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_hidden", type="boolean")
     */
    private $isHidden;

    /**
     * @var string
     *
     * @ORM\Column(name="english", type="string", length=255)
     */
    private $english;

    /**
     * @var string
     *
     * @ORM\Column(name="french", type="string", length=255)
     */
    private $french;

    /**
     * @var string
     *
     * @ORM\Column(name="help", type="string", length=255)
     */
    private $help;

    /**
     * @ORM\ManyToOne(targetEntity="Week")
     * @ORM\JoinColumn(name="week_id", referencedColumnName="id")
     */
    private $week;

    /**
     * Magic PHP constructor, set default values to any new instance
     *
     * @return Artist
     */
    public function __construct()
    {
        $this->createdAt = new DateTime();
        $this->updatedAt = new DateTime();
        $this->isHidden = false;

        return $this;
    }

    /**
     * Magic PHP toString()
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getEnglish();
    }

    /**
     * Instance helper
     *
     * @return string
     */
    public function toString()
    {
        return $this->getEnglish();
    }

    /**
     * Instance helper
     *
     * @return array
     */
    public function toArray($recursive = false)
    {
        $entityAsArray = get_object_vars($this);

        if ($recursive) {
            foreach ($entityAsArray as &$var) {
                if ((is_object($var)) && (method_exists($var, 'toArray'))) {
                    $var = $var->toArray($recursive);
                }
            }
        }

        return $entityAsArray;
    }

    /**
     * @see Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
        ));
    }

    /**
     * @see Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
        ) = unserialize($serialized);
    }

    /**
     * Instance helper
     *
     * @return boolean
     */
    public function isDeleted()
    {
        return is_null($this->removedAt);
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
     * Set createdAt
     *
     * @param DateTime $createdAt
     * @return Word
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param DateTime $updatedAt
     * @return Word
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set removedAt
     *
     * @param DateTime $removedAt
     * @return Word
     */
    public function setRemovedAt($removedAt)
    {
        $this->removedAt = $removedAt;

        return $this;
    }

    /**
     * Get removedAt
     *
     * @return DateTime
     */
    public function getRemovedAt()
    {
        return $this->removedAt;
    }

    /**
     * Set isHidden
     *
     * @param boolean $isHidden
     * @return Word
     */
    public function setIsHidden($isHidden)
    {
        $this->isHidden = $isHidden;

        return $this;
    }

    /**
     * Get isHidden
     *
     * @return boolean
     */
    public function getIsHidden()
    {
        return $this->isHidden;
    }

    /**
     * Set name
     *
     * @param string $english
     * @return Word
     */
    public function setEnglish($english)
    {
        $this->name = $english;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getEnglish()
    {
        return $this->name;
    }

    /**
     * Set french
     *
     * @param string $french
     * @return Word
     */
    public function setFrench($french)
    {
        $this->french = $french;

        return $this;
    }

    /**
     * Get french
     *
     * @return string
     */
    public function getFrench()
    {
        return $this->french;
    }

    /**
     * Set help
     *
     * @param string $help
     * @return Word
     */
    public function setHelp($help)
    {
        $this->help = $help;

        return $this;
    }

    /**
     * Get help
     *
     * @return string
     */
    public function getHelp()
    {
        return $this->help;
    }

    /**
     * Set week
     *
     * @param Week $week
     * @return Word
     */
    public function setWeek(Week $week = null)
    {
        $this->week = $week;

        return $this;
    }

    /**
     * Get week
     *
     * @return Week
     */
    public function getWeek()
    {
        return $this->week;
    }
}
