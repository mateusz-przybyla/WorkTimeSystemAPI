<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Uid\Uuid;

class WorkTimeDto
{
  public function __construct(
    #[SerializedName('uuid')]
    #[Assert\NotBlank(message: 'work.errors.blank')]
    public readonly ?Uuid $uuid,

    #[SerializedName('startTime')]
    #[Assert\NotBlank(message: 'work.errors.blank')]
    public readonly ?\DateTime $startTime,

    #[SerializedName('endTime')]
    #[Assert\NotBlank(message: 'work.errors.blank')]
    public readonly ?\DateTime $endTime
  ) {}

  public function validateWorkTimeBusinessLogic(): array
  {
    $errors = [];

    if ($this->endTime <= $this->startTime) {
      $errors[] = 'errors.datetime_invalid_order';
    }

    $duration = ($this->endTime->getTimestamp() - $this->startTime->getTimestamp()) / 3600;
    if ($duration > 12) {
      $errors[] = 'errors.datetime_too_long';
    }

    return $errors;
  }
}
