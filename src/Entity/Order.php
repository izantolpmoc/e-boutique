<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $number = null;

    #[ORM\Column]
    private ?bool $valid = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'relatedOrder', targetEntity: CommandLine::class, cascade:['persist', 'remove'])]
    private Collection $commandLine;

    public function __construct()
    {
        $this->commandLine = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(bool $valid): self
    {
        $this->valid = $valid;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, CommandLine>
     */
    public function getCommandLine(): Collection
    {
        return $this->commandLine;
    }

    public function addCommandLine(CommandLine $commandLine): self
    {
        if (!$this->commandLine->contains($commandLine)) {
            $this->commandLine->add($commandLine);
            $commandLine->setRelatedOrder($this);
        }

        return $this;
    }

    public function removeCommandLine(CommandLine $commandLine): self
    {
        if ($this->commandLine->removeElement($commandLine)) {
            // set the owning side to null (unless already changed)
            if ($commandLine->getRelatedOrder() === $this) {
                $commandLine->setRelatedOrder(null);
            }
        }

        return $this;
    }
}
