<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TagRepository")
 */
class Tag
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $word;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Tickets", inversedBy="tags")
     * @ORM\JoinTable(name="relation")
     */
    private $ticket;

    public function __construct()
    {
        $this->ticket = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWord(): ?string
    {
        return $this->word;
    }

    public function setWord(string $word): self
    {
        $this->word = $word;

        return $this;
    }

    /**
     * @return Collection|Tickets[]
     */
    public function getTicket(): Collection
    {
        return $this->ticket;
    }

    public function addTicket(Tickets $ticket): self
    {
        if (!$this->ticket->contains($ticket)) {
            $this->ticket[] = $ticket;
        }

        return $this;
    }

    public function removeTicket(Tickets $ticket): self
    {
        if ($this->ticket->contains($ticket)) {
            $this->ticket->removeElement($ticket);
        }

        return $this;
    }
}
