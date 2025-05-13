<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\SerializedName;

class WorkTimeDto
{
  public function __construct(
    #[SerializedName('unikalny identyfikator pracownika')]
    #[Assert\GreaterThan(value: 0, message: 'ID musi być liczbą większą od 0.')]
    public readonly int $employeeId,

    #[SerializedName('data i godzina rozpoczęcia')]
    public readonly \DateTime $startTime,

    #[SerializedName('data i godzina zakończenia')]
    public readonly \DateTime $endTime
  ) {}
}
