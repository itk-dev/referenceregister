<?php

namespace App\Entity;

use App\Entity\Department\ContactPerson;
use App\Entity\Department\LookupSlot;
use App\Repository\DepartmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints\Valid;

#[ORM\Entity(repositoryClass: DepartmentRepository::class)]
class Department implements \Stringable
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Entry>
     */
    #[ORM\OneToMany(targetEntity: Entry::class, mappedBy: 'department')]
    private Collection $entries;

    /**
     * @var Collection<int, ContactPerson>
     */
    #[ORM\OneToMany(targetEntity: ContactPerson::class, mappedBy: 'department', cascade: ['persist'], orphanRemoval: true)]
    #[Valid]
    private Collection $contactPeople;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'departments')]
    private Collection $users;

    #[ORM\Embedded(class: LookupSlot::class)]
    #[Valid]
    private ?LookupSlot $lookupSlot = null;

    public function __construct()
    {
        $this->entries = new ArrayCollection();
        $this->contactPeople = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    #[\Override]
    public function __toString(): string
    {
        return (string) $this->getName();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Entry>
     */
    public function getEntries(): Collection
    {
        return $this->entries;
    }

    public function addEntry(Entry $entry): static
    {
        if (!$this->entries->contains($entry)) {
            $this->entries->add($entry);
            $entry->setDepartment($this);
        }

        return $this;
    }

    public function removeEntry(Entry $entry): static
    {
        if ($this->entries->removeElement($entry)) {
            // set the owning side to null (unless already changed)
            if ($entry->getDepartment() === $this) {
                $entry->setDepartment(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ContactPerson>
     */
    public function getContactPeople(): Collection
    {
        return $this->contactPeople;
    }

    public function addContactPerson(ContactPerson $contactPerson): static
    {
        if (!$this->contactPeople->contains($contactPerson)) {
            $this->contactPeople->add($contactPerson);
            $contactPerson->setDepartment($this);
        }

        return $this;
    }

    public function removeContactPerson(ContactPerson $contactPerson): static
    {
        if ($this->contactPeople->removeElement($contactPerson)) {
            // set the owning side to null (unless already changed)
            if ($contactPerson->getDepartment() === $this) {
                $contactPerson->setDepartment(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addDepartment($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removeDepartment($this);
        }

        return $this;
    }

    public function getLookupSlot(): ?LookupSlot
    {
        return $this->lookupSlot;
    }

    public function setLookupSlot(LookupSlot $lookupSlot): Department
    {
        $this->lookupSlot = $lookupSlot;

        return $this;
    }
}
