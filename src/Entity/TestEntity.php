<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TestEntityRepository")
 */
class TestEntity
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $some_field;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSomeField(): ?string
    {
        return $this->some_field;
    }

    public function setSomeField(?string $some_field): self
    {
        $this->some_field = $some_field;

        return $this;
    }
}
