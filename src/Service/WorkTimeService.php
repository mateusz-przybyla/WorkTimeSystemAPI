<?php

namespace App\Service;

use App\Dto\WorkTimeDto;
use App\Entity\WorkTime;
use App\Repository\EmployeeRepository;
use App\Repository\WorkTimeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class WorkTimeService
{
  public function __construct(
    private EntityManagerInterface $entityManager,
    private EmployeeRepository $employeeRepo,
    private WorkTimeRepository $workTimeRepo
  ) {}

  public function register(WorkTimeDto $dto): array
  {
    $employee = $this->employeeRepo->find($dto->employeeId);
    if (!$employee) {
      throw new NotFoundHttpException('Nie znaleziono pracownika.');
    }

    $workDay = \DateTime::createFromFormat('Y-m-d', $dto->startTime->format('Y-m-d'));

    $existing = $this->workTimeRepo->findOneBy([
      'employee' => $employee,
      'workDay' => $workDay,
    ]);

    if ($existing) {
      throw new ConflictHttpException('Pracownik już posiada zarejestrowany czas pracy w tym dniu.');
    }

    $workTime = new WorkTime();
    $workTime->setEmployee($employee)
      ->setStartTime($dto->startTime)
      ->setEndTime($dto->endTime)
      ->setWorkDay($dto->startTime);

    $this->entityManager->persist($workTime);
    $this->entityManager->flush();

    return ['response' => ['Czas pracy został dodany.']];
  }
}
