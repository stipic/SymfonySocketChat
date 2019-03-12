<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $displayName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @var array $conversations
     * 
     * @ORM\ManyToMany(targetEntity="Conversation", inversedBy="users")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $conversations;

    /**
     * @var array $unreadedMessages
     * 
     * @ORM\ManyToMany(targetEntity="Message", inversedBy="unreadedBy")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    private $unreadedMessages;

    /**
     * @ORM\ManyToMany(targetEntity="Group", inversedBy="users")
     *
     */
    private $groups;

    public function __construct()
    {
        $this->conversations = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->unreadedMessages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(string $displayName): self
    {
        $this->displayName = $displayName;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getSalt()
    {
    }

    public function addGroup(\App\Entity\Group $group)
    {
        $this->groups[] = $group;

        return $this;
    }

    public function getRoles()
    {
        return $this->groups->toArray();
    }

    public function eraseCredentials()
    {

    }

    /**
     * Get $conversations
     *
     * @return  array
     */ 
    public function getConversations()
    {
        return $this->conversations;
    }

    /**
     * Set $conversations
     *
     * @param  array  $conversations  $conversations
     *
     * @return  self
     */ 
    public function setConversations(array $conversations)
    {
        $this->conversations = $conversations;

        return $this;
    }

    public function addConversation(\App\Entity\Conversation $conversation)
    {
        $this->conversations[] = $conversation;

        return $this;
    }

    public function removeConversation(\App\Entity\Conversation $conversation)
    {
        $this->conversations->removeElement($conversation);
    }

    /////////////////////////

    public function addUnreadedMessage(\App\Entity\Message $message)
    {
        $this->unreadedMessages[] = $message;

        return $this;
    }

    public function removeUnreadedMessage(\App\Entity\Message $message)
    {
        $this->unreadedMessages->removeElement($message);
    }

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // $this->salt,
        ));
    }

    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            // $this->salt
        ) = unserialize($serialized);
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersistSetCreatedAt()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * Get $unreadedMessages
     *
     * @return  array
     */ 
    public function getUnreadedMessages()
    {
        return $this->unreadedMessages;
    }
}