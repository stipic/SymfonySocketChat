<?php

namespace App\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Serializer\Filter\GroupFilter;
use ApiPlatform\Core\Annotation\ApiFilter;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="messages")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Message
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $messageBlock
     * 
     * @ORM\ManyToOne(targetEntity="MessageBlock", inversedBy="messages")
     * @ORM\JoinColumn(name="messageBlock", referencedColumnName="id")
     */
    private $messageBlock;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var string $createdBy
     *
	 * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     */
    private $createdBy;

    /**
     * @var string $updatedBy
     *
     * @Gedmo\Blameable(on="update")
	 * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="updated_by", referencedColumnName="id")
     */
    private $updatedBy;

    /**
     * @ORM\Column(type="text", name="content", options={"default": ""})
     */
    private $content;

    /**
     * @ORM\Column(name="deleted", type="boolean")
     */
    private $deleted;

    /**
     * users that didn't read this message.
     * 
     * @ORM\ManyToMany(targetEntity="User", mappedBy="unreadedMessages")
     */
    private $unreadedBy;

    /**
     * @var string $parsedContent
     */
    private $parsedContent;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\File", inversedBy="message", cascade={"persist", "remove"})
     */
    private $file;

    public function __construct()
    {
        $this->unreadedBy = new ArrayCollection();
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
     * Get the value of users that didnt read this message
     */ 
    public function getUnreadedBy()
    {
        return $this->unreadedBy;
    }

    public function addUserOnUnreadedList(\App\Entity\User $user)
    {
        $user->addUnreadedMessage($this);
        $this->unreadedBy->add($user);

        return $this->unreadedBy;
    }

    public function removeUserFromUnreadedList(\App\Entity\User $user)
    {
        $user->removeUnreadedMessage($this);
        $this->unreadedBy->removeElement($user);

        return $this->unreadedBy;
    }

    /**
     * Get the value of messageBlock
     */ 
    public function getMessageBlock()
    {
        return $this->messageBlock;
    }

    /**
     * Set the value of messageBlock
     *
     * @return  self
     */ 
    public function setMessageBlock($messageBlock)
    {
        $this->messageBlock = $messageBlock;

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
     * Get the value of deleted
     */ 
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set the value of deleted
     *
     * @return  self
     */ 
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get the value of content
     */ 
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set the value of content
     *
     * @return  self
     */ 
    public function setContent($content)
    {
        $this->content = $content;

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
     * Get $parsedContent
     *
     * @return  string
     */ 
    public function getParsedContent()
    {
        if(empty($this->parsedContent))
        {
            return $this->content;
        }
        return $this->parsedContent;
    }

    /**
     * Set $parsedContent
     *
     * @param  string  $parsedContent  $parsedContent
     *
     * @return  self
     */ 
    public function setParsedContent(string $parsedContent)
    {
        $this->parsedContent = $parsedContent;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;

        return $this;
    }
}