<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints\Length;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
#[ORM\Table(name: 'ludox_clients')]
#[UniqueEntity(fields: 'ci', message: 'El usuario con CI {{ value }} ya existe.')]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('client:get:simple')]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    #[Groups('client:get:simple')]
    private ?string $name = null;

    #[ORM\Column(length: 11, unique: true)]
    #[Length(min: 11, max: 11)]
    #[Groups('client:get:simple')]
    private ?string $ci = null;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Ticket::class, orphanRemoval: true)]
    private Collection $tickets;

    #[ORM\ManyToMany(targetEntity: Session::class, mappedBy: 'clients')]
    private Collection $sessions;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
        $this->sessions = new ArrayCollection();
    }

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

    public function isMale(): bool
    {
        $digit = (int)$this->ci[9];

        return $digit % 2 === 0;
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): static
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets->add($ticket);
            $ticket->setClient($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): static
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getClient() === $this) {
                $ticket->setClient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Session>
     */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    public function addSession(Session $session): static
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions->add($session);
            $session->addClient($this);
        }

        return $this;
    }

    public function removeSession(Session $session): static
    {
        if ($this->sessions->removeElement($session)) {
            $session->removeClient($this);
        }

        return $this;
    }
}
