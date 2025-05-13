<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\SerializedName;

class WorkTimeSummaryDto
{
  public function __construct(
    #[SerializedName('unikalny identyfikator pracownika')]
    #[Assert\GreaterThan(value: 0, message: 'ID musi być liczbą większą od 0.')]
    public readonly int $employeeId,

    #[SerializedName('data')]
    public readonly \DateTime $date
  ) {}
}
