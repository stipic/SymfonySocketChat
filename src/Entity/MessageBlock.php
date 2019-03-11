<?php

namespace App\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Serializer\Filter\GroupFilter;
use ApiPlatform\Core\Annotation\ApiFilter;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="message_blocks")
 * @ORM\HasLifecycleCallbacks()
 */
class MessageBlock
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var string $createdBy
     *
     * @Gedmo\Blameable(on="create")
	 * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     */
    private $createdBy;

    /**
     * @var array $messages
     * 
     * @ORM\OneToMany(targetEntity="Message", mappedBy="messageBlock", cascade={"persist","remove"})
     * @ORM\OrderBy({"createdAt" = "ASC"})
     */
    private $messages;

    /**
     * @var string $updatedBy
     *
     * @Gedmo\Blameable(on="update")
	 * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="updated_by", referencedColumnName="id")
     */
    private $updatedBy;

    /**
     * @ORM\ManyToOne(targetEntity="Conversation", inversedBy="messageBlocks")
     * @ORM\JoinColumn(name="conversation", referencedColumnName="id")
     */
    private $conversation;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
    }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Page
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
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
     * Set CreatedBy
     *
     * @param string $CreatedBy
     *
     * @return string
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     * Get CreatedBy
     *
     * @return string
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

	/**
     * Set UpdatedBy
     *
     * @param string $updatedBy
     *
     * @return string
     */
    public function setUpdatedBy($updatedBy)
    {
        $this->updatedBy = $updatedBy;
    }

    /**
     * Get UpdatedBy
	 * @param string $updatedBy
	 * 
     * @return string
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * Get the value of messages
     */ 
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Get the value of conversationId
     */ 
    public function getConversation()
    {
        return $this->conversation;
    }

    /**
     * Set the value of conversationId
     *
     * @return  self
     */ 
    public function setConversation($conversation)
    {
        $this->conversation = $conversation;

        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersistSetCreatedAt()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * Get the value of updatedAt
     */ 
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set the value of updatedAt
     *
     * @return  self
     */ 
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}