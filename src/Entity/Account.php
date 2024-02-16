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
    private ?\DateTimeImmutable $createdAt;

    #[ORM\Column(length: 16, unique: true)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $type;

    #[ORM\OneToMany(mappedBy: 'account', targetEntity: AccountEntry::class, orphanRemoval: true)]
    #[ORM\OrderBy(['createdAt' => 'DESC'])]
    private Collection $entries;

    public function __construct()
    {
        $this->entries = new ArrayCollection();

        $this->createdAt = new \DateTimeImmutable();
        $this->type = AccountTypeEnum::ACCOUNT_DEBIT;
    }

    public function __toString(): string
    {
        return $this->getCode() . ' - ' . $this->getName();
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

    public function getTypeName(): string
    {
        return AccountTypeEnum::getTypeName($this->getType());
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

    public function isDebit(): bool
    {
        return $this->getType() === AccountTypeEnum::ACCOUNT_DEBIT;
    }

    public function isCredit(): bool
    {
        return $this->getType() === AccountTypeEnum::ACCOUNT_CREDIT;
    }

    public function getDebit(): float
    {
        $debit = 0;

        foreach ($this->getEntries() as $entry) {
            $debit += $entry->getDebit() ?: 0;
        }

        return $debit;
    }

    public function getCredit(): float
    {
        $credit = 0;

        foreach ($this->getEntries() as $entry) {
            $credit += $entry->getCredit() ?: 0;
        }

        return $credit;
    }

    public function getBalance(): int
    {
        $debit = 0;
        $credit = 0;

        foreach ($this->getEntries() as $entry) {
            $debit += $entry->getDebit() ?: 0;
            $credit += $entry->getCredit() ?: 0;
        }

        $balance = $this->getType() === AccountTypeEnum::ACCOUNT_DEBIT ? $debit - $credit : $credit - $debit;

        return $balance;
    }

    public function getBalanceInPeriod(\DateTimeInterface $start, \DateTimeInterface $end): int
    {
        $debit = 0;
        $credit = 0;

        $entries = $this->getEntries()->filter(function (AccountEntry $entry) use ($start, $end) {
            return $entry->getAnnotatedAt() >= $start && $entry->getAnnotatedAt() <= $end;
        });

        foreach ($entries as $entry) {
            $debit += $entry->getDebit() ?: 0;
            $credit += $entry->getCredit() ?: 0;
        }

        $balance = $this->getType() === AccountTypeEnum::ACCOUNT_DEBIT ? $debit - $credit : $credit - $debit;

        return $balance;
    }
}
