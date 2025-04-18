<?php

declare(strict_types=1);

namespace Echtermax\PartyBundle\Entity;

class Party
{
    private int $id;
    private string $title;
    private string $description;
    private \DateTime $date;
    private string $location;
    private \DateTime $tstamp;
    private bool $published;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        
        return $this;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;
        
        return $this;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;
        
        return $this;
    }

    public function getTstamp(): \DateTime
    {
        return $this->tstamp;
    }

    public function setTstamp(\DateTime $tstamp): self
    {
        $this->tstamp = $tstamp;
        
        return $this;
    }

    public function isPublished(): bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): self
    {
        $this->published = $published;
        
        return $this;
    }
}
