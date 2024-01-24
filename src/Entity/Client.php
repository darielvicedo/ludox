<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\Length;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
#[ORM\Table(name: 'ludox_clients')]
#[UniqueEntity(fields: 'ci', message: 'El usuario con CI {{ value }} ya existe.')]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private ?string $name = null;

    #[ORM\Column(length: 11, unique: true)]
    #[Length(min: 11, max: 11)]
    private ?string $ci = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = strtoupper($name);

        return $this;
    }

    public function getCi(): ?string
    {
        return $this->ci;
    }

    public function setCi(string $ci): static
    {
        $this->ci = $ci;

        return $this;
    }

    /**
     * Calculates age from CI.
     *
     * @return int
     */
    public function getAge()
    {
        $datePart = substr($this->getCi(), 0, 6);
        $birthday = DateTimeImmutable::createFromFormat('ymd H:i', $datePart . ' 00:00');
        $now = new \DateTimeImmutable();
        $age = $now->diff($birthday);

        return $age->y;
    }
}
