<?php

namespace App\Dto;

use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\SerializedName;

class WorkTimeSummaryDto
{
  public function __construct(
    #[SerializedName('uuid')]
    #[Assert\NotBlank(message: 'UUID jest wymagane.')]
    public readonly ?Uuid $uuid,

    #[SerializedName('date')]
    #[Assert\NotBlank(message: 'Data jest wymagana.')]
    public readonly ?\DateTime $date
  ) {}
}
