<?php

namespace App\Service;

use App\Dto\EmployeeDto;
use App\Entity\Employee;
use Doctrine\ORM\EntityManagerInterface;

class EmployeeService
{
  public function __construct(private EntityManagerInterface $entityManager) {}

  public function create(EmployeeDto $dto): Employee
  {
    $employee = new Employee();
    $employee->setFirstname($dto->firstname);
    $employee->setSurname($dto->surname);

    $this->entityManager->persist($employee);
    $this->entityManager->flush();

    return $employee;
  }
}
