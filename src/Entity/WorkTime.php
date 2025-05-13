<?php

namespace App\Entity;

use App\Repository\WorkTimeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkTimeRepository::class)]
class WorkTime
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column]
  private ?\DateTime $startTime = null;

  #[ORM\Column]
  private ?\DateTime $endTime = null;

  #[ORM\Column(type: Types::DATE_MUTABLE)]
  private ?\DateTime $workDay = null;

  #[ORM\ManyToOne(inversedBy: 'workTimes')]
  #[ORM\JoinColumn(nullable: false)]
  private ?Employee $employee = null;

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getStartTime(): ?\DateTime
  {
    return $this->startTime;
  }

  public function setStartTime(\DateTime $startTime): static
  {
    $this->startTime = $startTime;

    return $this;
  }

  public function getEndTime(): ?\DateTime
  {
    return $this->endTime;
  }

  public function setEndTime(\DateTime $endTime): static
  {
    $this->endTime = $endTime;

    return $this;
  }

  public function getWorkDay(): ?\DateTime
  {
    return $this->workDay;
  }

  public function setWorkDay(\DateTime $workDay): static
  {
    $this->workDay = $workDay;

    return $this;
  }

  public function getEmployee(): ?Employee
  {
    return $this->employee;
  }

  public function setEmployee(?Employee $employee): static
  {
    $this->employee = $employee;

    return $this;
  }
}
