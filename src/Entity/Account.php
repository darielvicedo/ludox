<?php

namespace App\Entity;

use App\Enum\AccountTypeEnum;
use App\Repository\AccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: AccountRepository::class)]
#[ORM\Table(name: 'ludox_accounts')]
#[UniqueEntity(fields: 'code', message: 'La cuenta con código {{ value }} ya existe.')]
class Account
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 16, unique: true)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $type = null;

    #[ORM\OneToMany(mappedBy: 'account', targetEntity: AccountEntry::class, orphanRemoval: true)]
    private Collection $entries;

    public function __construct()
    {
        $this->entries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
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

    public function getType(): ?int
    {
        return $this->type;
    }

    /**
     * @param int $type
     * @return $this
     * @throws \Exception
     */
    public function setType(int $type): static
    {
        if (!AccountTypeEnum::isTypeCode($type)) {
            throw new \Exception("Unknown type.");
        }

        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, AccountEntry>
     */
    public function getEntries(): Collection
    {
        return $this->entries;
    }

    public function addEntry(AccountEntry $entry): static
    {
        if (!$this->entries->contains($entry)) {
            $this->entries->add($entry);
            $entry->setAccount($this);
        }

        return $this;
    }

    public function removeEntry(AccountEntry $entry): static
    {
        if ($this->entries->removeElement($entry)) {
            // set the owning side to null (unless already changed)
            if ($entry->getAccount() === $this) {
                $entry->setAccount(null);
            }
        }

        return $this;
    }
}
