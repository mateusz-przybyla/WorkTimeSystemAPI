<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\SerializedName;

class WorkTimeSummaryDto
{
  public function __construct(
    #[SerializedName('employee_id')]
    #[Assert\GreaterThan(value: 0, message: 'ID musi być liczbą większą od 0.')]
    public readonly int $employeeId,

    #[SerializedName('date')]
    public readonly \DateTime $date
  ) {}
}
