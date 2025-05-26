<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Uid\Uuid;

class WorkTimeDto
{
  public function __construct(
    #[SerializedName('uuid')]
    #[Assert\NotBlank(message: 'UUID jest wymagane.')]
    public readonly ?Uuid $uuid,

    #[SerializedName('startTime')]
    #[Assert\NotBlank(message: 'Data i godzina rozpoczęcia pracy jest wymagana.')]
    public readonly ?\DateTime $startTime,

    #[SerializedName('endTime')]
    #[Assert\NotBlank(message: 'Data i godzina zakończenia pracy jest wymagana.')]
    public readonly ?\DateTime $endTime
  ) {}

  public function validateWorkTimeBusinessLogic(): array
  {
    $errors = [];

    if ($this->endTime <= $this->startTime) {
      $errors[] = 'Data zakończenia musi być po dacie rozpoczęcia.';
    }

    $duration = ($this->endTime->getTimestamp() - $this->startTime->getTimestamp()) / 3600;
    if ($duration > 12) {
      $errors[] = 'Czas pracy nie może przekraczać 12 godzin.';
    }

    return $errors;
  }
}
