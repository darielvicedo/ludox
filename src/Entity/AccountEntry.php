<?php

namespace App\Entity;

use App\Repository\AccountEntryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccountEntryRepository::class)]
#[ORM\Table(name: 'ludox_entries')]
class AccountEntry
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $annotatedAt = null;

    #[ORM\Column(length: 255)]
    private ?string $concept = null;

    #[ORM\Column(nullable: true)]
    private ?int $debit = null;

    #[ORM\Column(nullable: true)]
    private ?int $credit = null;

    #[ORM\ManyToOne(inversedBy: 'entries')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Account $account = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
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

    public function getAnnotatedAt(): ?\DateTimeImmutable
    {
        return $this->annotatedAt;
    }

    public function setAnnotatedAt(\DateTimeImmutable $annotatedAt): static
    {
        $this->annotatedAt = $annotatedAt;

        return $this;
    }

    public function getConcept(): ?string
    {
        return $this->concept;
    }

    public function setConcept(string $concept): static
    {
        $this->concept = $concept;

        return $this;
    }

    public function getDebit(): ?int
    {
        return $this->debit;
    }

    public function setDebit(?int $debit): static
    {
        $this->debit = $debit;

        return $this;
    }

    public function getCredit(): ?int
    {
        return $this->credit;
    }

    public function setCredit(?int $credit): static
    {
        $this->credit = $credit;

        return $this;
    }

    public function getAccount(): ?Account
    {
        return $this->account;
    }

    public function setAccount(?Account $account): static
    {
        $this->account = $account;

        return $this;
    }
}
