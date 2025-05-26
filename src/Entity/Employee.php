<?php

namespace App\Entity;

use App\Repository\EmployeeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
class Employee
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  private ?string $firstname = null;

  #[ORM\Column(length: 255)]
  private ?string $surname = null;

  /**
   * @var Collection<int, WorkTime>
   */
  #[ORM\OneToMany(targetEntity: WorkTime::class, mappedBy: 'employee', orphanRemoval: true)]
  private Collection $workTimes;

  #[ORM\Column(type: 'uuid', unique: true)]
  private ?Uuid $uuid = null;

  public function __construct()
  {
    $this->uuid = Uuid::v4();
    $this->workTimes = new ArrayCollection();
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getFirstname(): ?string
  {
    return $this->firstname;
  }

  public function setFirstname(string $firstname): static
  {
    $this->firstname = $firstname;

    return $this;
  }

  public function getSurname(): ?string
  {
    return $this->surname;
  }

  public function setSurname(string $surname): static
  {
    $this->surname = $surname;

    return $this;
  }

  /**
   * @return Collection<int, WorkTime>
   */
  public function getWorkTimes(): Collection
  {
    return $this->workTimes;
  }

  public function addWorkTime(WorkTime $workTime): static
  {
    if (!$this->workTimes->contains($workTime)) {
      $this->workTimes->add($workTime);
      $workTime->setEmployee($this);
    }

    return $this;
  }

  public function removeWorkTime(WorkTime $workTime): static
  {
    if ($this->workTimes->removeElement($workTime)) {
      // set the owning side to null (unless already changed)
      if ($workTime->getEmployee() === $this) {
        $workTime->setEmployee(null);
      }
    }

    return $this;
  }

  public function getUuid(): ?Uuid
  {
    return $this->uuid;
  }
}
