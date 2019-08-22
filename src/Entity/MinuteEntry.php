<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MinuteEntryRepository")
 */
class MinuteEntry
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateTime;

    /**
     * @ORM\Column(type="smallint")
     */
    private $occupancy;

    /**
     * @ORM\Column(type="smallint")
     */
    private $totalIn;

    /**
     * @ORM\Column(type="smallint")
     */
    private $totalOut;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Location")
     * @ORM\JoinColumn(nullable=false)
     */
    private $location;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateTime(): ?\DateTimeInterface
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTimeInterface $dateTime): self
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function getOccupancy(): ?int
    {
        return $this->occupancy;
    }

    public function setOccupancy(int $occupancy): self
    {
        $this->occupancy = $occupancy;

        return $this;
    }

    public function getTotalIn(): ?int
    {
        return $this->totalIn;
    }

    public function setTotalIn(int $totalIn): self
    {
        $this->totalIn = $totalIn;

        return $this;
    }

    public function getTotalOut(): ?int
    {
        return $this->totalOut;
    }

    public function setTotalOut(int $totalOut): self
    {
        $this->totalOut = $totalOut;

        return $this;
    }

    public function getLocation(): ?Location
    {
        return $this->location;
    }

    public function setLocation(?Location $location): self
    {
        $this->location = $location;

        return $this;
    }
}
