<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\SerializedName;

class WorkTimeDto
{
  public function __construct(
    #[SerializedName('uuid')]
    #[Assert\Uuid(message: 'UUID musi być poprawnym identyfikatorem.')]
    public readonly string $uuid,

    #[SerializedName('starting_date_and_time')]
    public readonly \DateTime $startTime,

    #[SerializedName('closing_date_and_time')]
    public readonly \DateTime $endTime
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
