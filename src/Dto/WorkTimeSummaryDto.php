<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\SerializedName;

class WorkTimeSummaryDto
{
  public function __construct(
    #[SerializedName('uuid')]
    #[Assert\Uuid(message: 'UUID musi być poprawnym identyfikatorem.')]
    public readonly string $uuid,

    #[SerializedName('date')]
    public readonly \DateTime $date
  ) {}
}
