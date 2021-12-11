<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 */
class Task
{
    const ALIAS = 'task';
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $state;


    /**
     * @ORM\Column(type="boolean")
     */
    private $viewOperator;

    /**
     * @ORM\Column(type="string", length=6000)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=6000, nullable=true)
     */
    private $material;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="task")
     */
    private $iduser;

    /**
     * @ORM\ManyToOne(targetEntity=Service::class, inversedBy="tasks")
     */
    private $service;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="task")
     */
    private $company;

    public function __construct()
    {
        $this->iduser = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     */
    public function setState($state): void
    {
        $this->state = $state;
    }

    /**
     * @return mixed
     */
    public function getViewOperator()
    {
        return $this->viewOperator;
    }

    /**
     * @param mixed $viewOperator
     */
    public function setViewOperator($viewOperator): void
    {
        $this->viewOperator = $viewOperator;
    }


    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getMaterial(): ?string
    {
        return $this->material;
    }

    public function setMaterial(?string $material): self
    {
        $this->material = $material;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getIduser(): Collection
    {
        return $this->iduser;
    }

    public function addIduser(User $iduser): self
    {
        if (!$this->iduser->contains($iduser)) {
            $this->iduser[] = $iduser;
        }

        return $this;
    }

    public function removeIduser(User $iduser): self
    {
        $this->iduser->removeElement($iduser);

        return $this;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }
}
