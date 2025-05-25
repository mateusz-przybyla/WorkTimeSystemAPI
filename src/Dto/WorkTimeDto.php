<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\SerializedName;

class WorkTimeDto
{
  public function __construct(
    #[SerializedName('employee_id')]
    #[Assert\GreaterThan(value: 0, message: 'ID musi być liczbą większą od 0.')]
    public readonly int $employeeId,

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
