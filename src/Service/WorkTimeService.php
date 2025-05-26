<?php

namespace App\Service;

use App\Dto\WorkTimeDto;
use App\Entity\WorkTime;
use App\Dto\WorkTimeSummaryDto;
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
    private WorkTimeRepository $workTimeRepo,
    private float $workRate,
    private int $monthlyStandardHours,
    private float $overtimeMultiplier
  ) {}

  public function register(WorkTimeDto $dto): array
  {
    $employee = $this->employeeRepo->findOneByUuid($dto->uuid);
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

  public function summarizeDay(WorkTimeSummaryDto $dto): array
  {
    $employee = $this->employeeRepo->findOneByUuid($dto->uuid);
    if (!$employee) {
      throw new NotFoundHttpException('Nie znaleziono pracownika.');
    }

    $workDay = \DateTime::createFromFormat('Y-m-d', $dto->date->format('Y-m-d'));
    $workTimes = $this->workTimeRepo->findBy(['employee' => $employee, 'workDay' => $workDay]);

    $totalHours = $this->calculateTotalRoundedHours($workTimes);

    return [
      'response' => [
        'suma po przeliczeniu' => ($totalHours * $this->workRate) . ' PLN',
        'ilość godzin z danego dnia' => $totalHours,
        'stawka' => $this->workRate . ' PLN'
      ]
    ];
  }

  public function summarizeMonth(WorkTimeSummaryDto $dto): array
  {
    $employee = $this->employeeRepo->findOneByUuid($dto->uuid);
    if (!$employee) {
      throw new NotFoundHttpException('Nie znaleziono pracownika.');
    }

    $start = (clone $dto->date)->modify('first day of this month')->setTime(0, 0);
    $end = (clone $dto->date)->modify('last day of this month')->setTime(23, 59);
    $workTimes = $this->workTimeRepo->findForPeriod($employee, $start, $end);

    $totalHours = $this->calculateTotalRoundedHours($workTimes);

    $normalHours = min($this->monthlyStandardHours, $totalHours);
    $overtimeHours = max(0, $totalHours - $this->monthlyStandardHours);

    return [
      'response' => [
        'ilość normalnych godzin z danego miesiąca' => $normalHours,
        'stawka' => $this->workRate . ' PLN',
        'ilość nadgodzin z danego miesiąca' => $overtimeHours,
        'stawka nadgodzinowa' => $this->workRate * $this->overtimeMultiplier . ' PLN',
        'suma po przeliczeniu' => ($this->calculateSalary($normalHours, $overtimeHours)) . ' PLN',
      ]
    ];
  }

  private function calculateSalary(float $normalHours, float $overtimeHours): float
  {
    return $normalHours * $this->workRate + $overtimeHours * $this->workRate * $this->overtimeMultiplier;
  }

  private function calculateTotalRoundedHours(array $workTimes): float
  {
    $totalHours = 0;
    foreach ($workTimes as $entry) {
      $interval = $entry->getStartTime()->diff($entry->getEndTime());
      $hours = $this->roundToNearestHalfHour($interval->h + $interval->i / 60);
      $totalHours += $hours;
    }

    return $totalHours;
  }

  private function roundToNearestHalfHour(float $hours): float
  {
    return round($hours * 2) / 2;
  }
}
