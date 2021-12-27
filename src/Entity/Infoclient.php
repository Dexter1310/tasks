<?php

namespace App\Entity;

use App\Repository\InfoclientRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InfoclientRepository::class)
 */
class Infoclient
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="infoclient", cascade={"persist", "remove"})
     */
    private $idUser;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $town;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $province;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $number;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $CP;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $otherTlf;

    /**
     * @ORM\Column(type="string", length=6000, nullable=true)
     */
    private $infoExtra;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUser(): ?User
    {
        return $this->idUser;
    }

    public function setIdUser(?User $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTown()
    {
        return $this->town;
    }

    /**
     * @param mixed $town
     */
    public function setTown($town): void
    {
        $this->town = $town;
    }


    public function getProvince(): ?string
    {
        return $this->province;
    }

    public function setProvince(?string $province): self
    {
        $this->province = $province;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getCP(): ?string
    {
        return $this->CP;
    }

    public function setCP(?string $CP): self
    {
        $this->CP = $CP;

        return $this;
    }

    public function getOtherTlf(): ?string
    {
        return $this->otherTlf;
    }

    public function setOtherTlf(?string $otherTlf): self
    {
        $this->otherTlf = $otherTlf;

        return $this;
    }

    public function getInfoExtra(): ?string
    {
        return $this->infoExtra;
    }

    public function setInfoExtra(?string $infoExtra): self
    {
        $this->infoExtra = $infoExtra;

        return $this;
    }
}
