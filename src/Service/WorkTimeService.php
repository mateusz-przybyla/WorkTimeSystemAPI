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

  public function register(WorkTimeDto $dto): void
  {
    $employee = $this->employeeRepo->findOneByUuid($dto->uuid);
    if (!$employee) {
      throw new NotFoundHttpException('employee_not_found');
    }

    $workDay = \DateTime::createFromFormat('Y-m-d', $dto->startTime->format('Y-m-d'));

    $existing = $this->workTimeRepo->findOneBy([
      'employee' => $employee,
      'workDay' => $workDay,
    ]);

    if ($existing) {
      throw new ConflictHttpException('worktime_duplicate_for_day');
    }

    $workTime = new WorkTime();
    $workTime->setEmployee($employee)
      ->setStartTime($dto->startTime)
      ->setEndTime($dto->endTime)
      ->setWorkDay($dto->startTime);

    $this->entityManager->persist($workTime);
    $this->entityManager->flush();
  }

  public function summarizeDay(WorkTimeSummaryDto $dto): array
  {
    $employee = $this->employeeRepo->findOneByUuid($dto->uuid);
    if (!$employee) {
      throw new NotFoundHttpException('employee_not_found');
    }

    $workDay = \DateTime::createFromFormat('Y-m-d', $dto->date->format('Y-m-d'));
    $workTimes = $this->workTimeRepo->findBy(['employee' => $employee, 'workDay' => $workDay]);

    $totalHours = $this->calculateTotalRoundedHours($workTimes);

    return [
      'response' => [
        'total_after_conversion' => ($totalHours * $this->workRate) . ' PLN',
        'hours_for_given_day' => $totalHours,
        'rate' => $this->workRate . ' PLN'
      ]
    ];
  }

  public function summarizeMonth(WorkTimeSummaryDto $dto): array
  {
    $employee = $this->employeeRepo->findOneByUuid($dto->uuid);
    if (!$employee) {
      throw new NotFoundHttpException('employee_not_found');
    }

    $start = (clone $dto->date)->modify('first day of this month')->setTime(0, 0);
    $end = (clone $dto->date)->modify('last day of this month')->setTime(23, 59);
    $workTimes = $this->workTimeRepo->findForPeriod($employee, $start, $end);

    $totalHours = $this->calculateTotalRoundedHours($workTimes);

    $normalHours = min($this->monthlyStandardHours, $totalHours);
    $overtimeHours = max(0, $totalHours - $this->monthlyStandardHours);

    return [
      'response' => [
        'regular_hours_in_given_month' => $normalHours,
        'rate' => $this->workRate . ' PLN',
        'overtime_hours_in_given_month' => $overtimeHours,
        'overtime_rate' => $this->workRate * $this->overtimeMultiplier . ' PLN',
        'total_after_conversion' => ($this->calculateSalary($normalHours, $overtimeHours)) . ' PLN',
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
