<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\SerializedName;

class EmployeeDto
{
  public function __construct(
    #[SerializedName('firstname')]
    #[Assert\NotBlank(message: 'employee.errors.blank')]
    public readonly string $firstname,

    #[SerializedName('surname')]
    #[Assert\NotBlank(message: 'employee.errors.blank')]
    public readonly string $surname
  ) {}
}
