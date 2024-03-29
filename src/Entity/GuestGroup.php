<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GuestGroupRepository")
 */
class GuestGroup
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slugUrl;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Person", mappedBy="guestGroup")
     */
    private $people;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Person", inversedBy="contactGuestGroup", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $contactPerson;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Wedding", inversedBy="guestGroups")
     * @ORM\JoinColumn(nullable=false)
     */
    private $wedding;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Gift", mappedBy="guestGroup")
     */
    private $gifts;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Event", inversedBy="guestGroups")
     */
    private $event;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $mailStatus;

    public function __construct()
    {
        $this->people = new ArrayCollection();
        $this->gifts = new ArrayCollection();
        $this->event = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getSlugUrl(): ?string
    {
        return $this->slugUrl;
    }

    public function setSlugUrl(?string $slugUrl): self
    {
        $this->slugUrl = $slugUrl;

        return $this;
    }

    /**
     * @return Collection|Person[]
     */
    public function getPeople(): Collection
    {
        return $this->people;
    }

    public function addPerson(Person $person): self
    {
            $this->people[] = $person;
            $person->setGuestGroup($this);

        return $this;
    }

    public function removePerson(Person $person): self
    {
        if ($this->people->contains($person)) {
            $this->people->removeElement($person);
            // set the owning side to null (unless already changed)
            if ($person->getGuestGroup() === $this) {
                $person->setGuestGroup(null);
            }
        }

        return $this;
    }

    public function getContactPerson(): ?Person
    {
        return $this->contactPerson;
    }

    public function setContactPerson(Person $contactPerson): self
    {
        $this->contactPerson = $contactPerson;

        return $this;
    }

    public function getWedding(): ?Wedding
    {
        return $this->wedding;
    }

    public function setWedding(?Wedding $wedding): self
    {
        $this->wedding = $wedding;

        return $this;
    }

    /**
     * @return Collection|Gift[]
     */
    public function getGifts(): Collection
    {
        return $this->gifts;
    }

    public function addGift(Gift $gift): self
    {
        if (!$this->gifts->contains($gift)) {
            $this->gifts[] = $gift;
            $gift->setGuestGroup($this);
        }

        return $this;
    }

    public function removeGift(Gift $gift): self
    {
        if ($this->gifts->contains($gift)) {
            $this->gifts->removeElement($gift);
            // set the owning side to null (unless already changed)
            if ($gift->getGuestGroup() === $this) {
                $gift->setGuestGroup(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Event[]
     */
    public function getEvent(): Collection
    {
        return $this->event;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->event->contains($event)) {
            $this->event[] = $event;
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->event->contains($event)) {
            $this->event->removeElement($event);
        }

        return $this;
    }

    public function getMailStatus(): ?bool
    {
        return $this->mailStatus;
    }

    public function setMailStatus(?bool $mailStatus): self
    {
        $this->mailStatus = $mailStatus;

        return $this;
    }

}
