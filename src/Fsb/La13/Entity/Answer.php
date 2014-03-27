<?php

namespace Fsb\La13\Entity;

use DateTime;
use Serializable;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * Answer
 *
 * @ORM\Table(name="words")
 * @ORM\Entity(repositoryClass="Fsb\Palmeraie\Entity\AnswerRepository")
 */
class Answer implements Serializable
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
     * @ORM\Column(name="is_correct", type="boolean")
     */
    private $isCorrect;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Word")
     * @ORM\JoinColumn(name="word_id", referencedColumnName="id")
     */
    private $word;

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
        return $this->getAnswer() . ' par ' . $this->getUser() . ' : ' . $this->getIsCorrect();
    }

    /**
     * Instance helper
     *
     * @return string
     */
    public function toString()
    {
        return $this->getAnswer() . ' par ' . $this->getUser() . ' : ' . $this->getIsCorrect();
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
     * @return Answer
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
     * @return Answer
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
     * @return Answer
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
     * @return Answer
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
     * Set isCorrect
     *
     * @param boolean $isCorrect
     * @return Answer
     */
    public function setIsCorrect($isCorrect)
    {
        $this->isCorrect = $isCorrect;

        return $this;
    }

    /**
     * Get isCorrect
     *
     * @return boolean
     */
    public function getIsCorrect()
    {
        return $this->isCorrect;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return Answer
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set word
     *
     * @param Word $word
     * @return Answer
     */
    public function setWord(Week $word)
    {
        $this->word = $word;

        return $this;
    }

    /**
     * Get word
     *
     * @return Week
     */
    public function getWord()
    {
        return $this->week;
    }
}
