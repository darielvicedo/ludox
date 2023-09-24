<?php

namespace App\Entity;

use App\Enum\AssetCategoryTypeEnum;
use App\Repository\AssetRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: AssetRepository::class)]
#[ORM\Table(name: 'ludox_assets')]
#[UniqueEntity(fields: 'code', message: 'El activo con código {{ value }} ya existe.')]
class Asset
{
    public const FIXED_ASSETS_ACCOUNTS = [
        '10.3.240',
        '10.3.241',
        '10.3.242',
        '10.3.243',
        '10.3.244',
        '10.3.245',
        '10.3.246',
        '10.3.247',
        '10.3.248',
        '10.3.249',
        '10.3.250',
        '10.3.251',
        '10.3.252',
        '10.3.253',
        '10.3.254',
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $lastMovementAt = null;

    #[ORM\Column(length: 5, unique: true)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $category = null;

    #[ORM\Column]
    private ?int $price = null;

    #[ORM\Column]
    private ?int $cost = null;

    #[ORM\ManyToOne(inversedBy: 'assets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Location $location = null;

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

    public function getLastMovementAt(): ?\DateTimeImmutable
    {
        return $this->lastMovementAt;
    }

    public function setLastMovementAt(?\DateTimeImmutable $lastMovementAt): static
    {
        $this->lastMovementAt = $lastMovementAt;

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

    public function getCategory(): ?int
    {
        return $this->category;
    }

    /**
     * @param int $category
     * @return $this
     * @throws \Exception
     */
    public function setCategory(int $category): static
    {
        if (!AssetCategoryTypeEnum::isTypeCode($category)) {
            throw new \Exception("Unknown type.");
        }

        $this->category = $category;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getCost(): ?int
    {
        return $this->cost;
    }

    public function setCost(int $cost): static
    {
        $this->cost = $cost;

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getValue(): int
    {
        return $this->getPrice() + $this->getCost();
    }

    public function getCategoryName(): ?string
    {
        return AssetCategoryTypeEnum::getTypeName($this->getCategory());
    }
}
