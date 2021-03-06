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
     * @ORM\Column(type="string", length=255)
     */
    private $time;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $time_end;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $time_total;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updatedAt;

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
     * @ORM\Column(type="string", length=255,nullable=true)
     */
    private $periodic;


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

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="tasks")
     */
    private $idClient;

    /**
     * @ORM\Column(type="string", name="imgTask", length=255,nullable=true)
     */
    private $imgTask;


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
     * @return mixed
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param mixed $time
     */
    public function setTime($time): void
    {
        $this->time = $time;
    }

    /**
     * @return mixed
     */
    public function getTimeEnd()
    {
        return $this->time_end;
    }

    /**
     * @param mixed $time_end
     */
    public function setTimeEnd($time_end): void
    {
        $this->time_end = $time_end;
    }

    /**
     * @return mixed
     */
    public function getTimeTotal()
    {
        return $this->time_total;
    }

    /**
     * @param mixed $time_total
     */
    public function setTimeTotal($time_total): void
    {
        $this->time_total = $time_total;
    }


    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        $this->setUpdatedAt(new \DateTime('now'));

        if ($this->getCreatedAt() == null) {
            $this->setCreatedAt(new \DateTime('now'));
        }
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
     * @return mixed
     */
    public function getPeriodic()
    {
        return $this->periodic;
    }

    /**
     * @param mixed $periodic
     */
    public function setPeriodic($periodic): void
    {
        $this->periodic = $periodic;
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

    public function getIdClient(): ?User
    {
        return $this->idClient;
    }

    public function setIdClient(?User $idClient): self
    {
        $this->idClient = $idClient;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getImgTask()
    {
        return $this->imgTask;
    }

    /**
     * @param mixed $imgTask
     */
    public function setImgTask($imgTask): void
    {
        $this->imgTask = $imgTask;
    }


}
