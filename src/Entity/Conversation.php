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
 * @ORM\Table(name="conversations")
 * @ORM\HasLifecycleCallbacks()
 */
class Conversation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    
    /**
     * Naziv koji ce se prikazivati onom tko je kreirao razgovor
     * Kada se korisnik registrira ili bude registriran on kreira razgovore između sebe i svakog drugog korisnika
     * Njemu cemo pokazati ime drugog korisnika
     * 
     * @ORM\Column(type="string", length=255, options={"default": ""}, nullable=true)
     */
    private $conversationNameForOwner;

    /**
     * Naziv koji ce se prikazivati onom tko je ubacen u ovaj razgovor od strane nekog drugog korisnika
     * Njemu cemo prikazivati ime vlasnika razgovora
     * 
     * @ORM\Column(type="string", length=255, options={"default": ""}, nullable=true)
     */
    private $conversationNameForGuest;

    /**
     * Naziv razgovora ukoliko je "isChannel" = TRUE, kanal u kojem netko tko ima prava ubaci druge ljude
     * Svima ce se prikazivati ovaj naziv 
     * 
     * Kanal ce se moci kreirati, sve što korisnici kroz aplikaciju kreiraju je KANAL
     * Kanal može imati 1,2,...100 korisnika
     * 
     * Kod kreiranja kanala ce se moci odabrati usere koji ce dobiti pristup kanalu ILI samo oznaciti da je kanal JAVAN
     * Ukoliko se kod kreiranja odaberu korisnici za kanal to znaci da je kanal isChannelPublic = FALSE
     * 
     * Ukoliko se kod kreiranja odabere da je kanal JAVAN, to znaci da je isChannelPublic = TRUE
     * 
     * Kod kreiranja JAVNOG kanala svi korisnici se ubacuju u njega
     * Kod registracije novog korisnika, trcimo kroz sve javne kanale i ubacujemo ga automatski u njega.
     * 
     * Znaci za kanal prikazujemo ovaj naziv $channelName
     * 
     * !!UPDATE!! - UKOLIKO JE KANAL i ukoliko je PRIVATAN u tom slučaju ne mora imati postavljen NAZIV
     * u tom slučaju ćemo za svakog korisnika renderirati naziv npr. ako sam ja user Kristijan i u kanalu sam koji nema postavljen
     * channelName i u tom kanalu je još Pero i Ivan, za mene (kristijana) ce naziv automatski biti:
     * 
     * "Pero, Ivan, Ja"
     * 
     * A za Peru:
     * 
     * "Kristijan, Ivan, Ja",
     * 
     * A za Ivana
     * 
     * "Ivan, Kristijan, Ja"
     * 
     * KOD JAVNIH KANALA, NARAVNO, naziv je !!OBAVEZAN!!
     * 
     * @ORM\Column(type="string", length=255, options={"default": ""}, nullable=true)
     */
    private $channelName;

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
     * @ORM\OneToMany(targetEntity="Message", mappedBy="conversation", cascade={"persist","remove"})
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
     * @ORM\Column(name="is_channel", type="boolean")
     */
    private $isChannel;

    /**
     * @ORM\Column(name="is_channel_public", type="boolean", nullable=true)
     */
    private $isChannelPublic;

    /**
     * @ORM\Column(name="deleted", type="boolean")
     */
    private $deleted;

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="conversations")
     */
    private $users;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->users = new ArrayCollection();
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
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Page
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
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
     * Get the value of isChannel
     */ 
    public function getIsChannel()
    {
        return $this->isChannel;
    }

    /**
     * Set the value of isChannel
     *
     * @return  self
     */ 
    public function setIsChannel($isChannel)
    {
        $this->isChannel = $isChannel;

        return $this;
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
     * Get the value of created
     */ 
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set the value of created
     *
     * @return  self
     */ 
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get the value of messages
     */ 
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Get the value of users
     */ 
    public function getUsers()
    {
        return $this->users;
    }

    public function addUserToConversation(\App\Entity\User $user)
    {
        $user->addConversation($this);
        $this->users->add($user);

        return $this->users;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersistSetCreatedAt()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * Get naziv koji ce se prikazivati onom tko je kreirao razgovor
     */ 
    public function getConversationNameForOwner()
    {
        return $this->conversationNameForOwner;
    }

    /**
     * Set naziv koji ce se prikazivati onom tko je kreirao razgovor
     *
     * @return  self
     */ 
    public function setConversationNameForOwner($conversationNameForOwner)
    {
        $this->conversationNameForOwner = $conversationNameForOwner;

        return $this;
    }

    /**
     * Get naziv koji ce se prikazivati onom tko je ubacen u ovaj razgovor od strane nekog drugog korisnika
     */ 
    public function getConversationNameForGuest()
    {
        return $this->conversationNameForGuest;
    }

    /**
     * Set naziv koji ce se prikazivati onom tko je ubacen u ovaj razgovor od strane nekog drugog korisnika
     *
     * @return  self
     */ 
    public function setConversationNameForGuest($conversationNameForGuest)
    {
        $this->conversationNameForGuest = $conversationNameForGuest;

        return $this;
    }

    /**
     * Get naziv razgovora ukoliko je "isChannel" = TRUE, kanal u kojem netko tko ima prava ubaci druge ljude
     */ 
    public function getChannelName()
    {
        return $this->channelName;
    }

    /**
     * Set naziv razgovora ukoliko je "isChannel" = TRUE, kanal u kojem netko tko ima prava ubaci druge ljude
     *
     * @return  self
     */ 
    public function setChannelName($channelName)
    {
        $this->channelName = $channelName;

        return $this;
    }

    /**
     * Get the value of isChannelPublic
     */ 
    public function getIsChannelPublic()
    {
        return $this->isChannelPublic;
    }

    /**
     * Set the value of isChannelPublic
     *
     * @return  self
     */ 
    public function setIsChannelPublic($isChannelPublic)
    {
        $this->isChannelPublic = $isChannelPublic;

        return $this;
    }
}