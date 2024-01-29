<?php

namespace App\Entity;

use App\Enum\CouponTypeEnum;
use App\Repository\CouponRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CouponRepository::class)]
#[ORM\Table(name: 'ludox_coupons')]
class Coupon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt;

    #[ORM\Column(length: 6)]
    private ?string $code = null;

    #[ORM\Column]
    private ?int $type = null;

    #[ORM\Column]
    private ?int $value = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $consumedAt = null;

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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function getTypeName(): string
    {
        return CouponTypeEnum::getTypeName($this->getType());
    }

    /**
     * @param int $type
     * @return $this
     * @throws \Exception
     */
    public function setType(int $type): static
    {
        if (!CouponTypeEnum::isTypeCode($type)) {
            throw new \Exception("Unknown type code.");
        }

        $this->type = $type;

        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getConsumedAt(): ?\DateTimeImmutable
    {
        return $this->consumedAt;
    }

    public function setConsumedAt(?\DateTimeImmutable $consumedAt): static
    {
        $this->consumedAt = $consumedAt;

        return $this;
    }

    public function getPromoName(): string
    {
        return match ($this->getType()) {
            CouponTypeEnum::COUPON_PERCENT_DISCOUNT => sprintf("Descuento del %d%%", $this->getValue()),
        };
    }
}
