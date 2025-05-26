<?php

namespace App\Tests\Service;

use App\Dto\WorkTimeSummaryDto;
use App\Entity\Employee;
use App\Entity\WorkTime;
use App\Repository\EmployeeRepository;
use App\Repository\WorkTimeRepository;
use App\Service\WorkTimeService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class WorkTimeServiceTest extends TestCase
{
  private WorkTimeService $service;
  private EmployeeRepository $employeeRepo;
  private WorkTimeRepository $workTimeRepo;
  private EntityManagerInterface $entityManager;

  protected function setUp(): void
  {
    $this->employeeRepo = $this->createMock(EmployeeRepository::class);
    $this->workTimeRepo = $this->createMock(WorkTimeRepository::class);
    $this->entityManager = $this->createMock(EntityManagerInterface::class);

    $workRate = 20;
    $monthlyStandardHours = 40;
    $overtimeMultiplier = 2.0;

    $this->service = new WorkTimeService(
      entityManager: $this->entityManager,
      employeeRepo: $this->employeeRepo,
      workTimeRepo: $this->workTimeRepo,
      workRate: $workRate,
      monthlyStandardHours: $monthlyStandardHours,
      overtimeMultiplier: $overtimeMultiplier
    );
  }

  public function testSummarizeMonthReturnsCorrectResponse(): void
  {
    $uuid = "c6e35e8f-95a6-4af2-839f-ad0a90d23266";
    $date = new \DateTime('2024-05');

    $dto = new WorkTimeSummaryDto(
      uuid: $uuid,
      date: $date
    );

    $employee = $this->createMock(Employee::class);
    $this->employeeRepo
      ->method('findOneByUuid')
      ->with($uuid)
      ->willReturn($employee);

    $workTime1 = new WorkTime();
    $workTime1->setStartTime(new \DateTime('2024-05-02 08:00'));
    $workTime1->setEndTime(new \DateTime('2024-05-02 16:00'));
    $workTime1->setWorkDay(new \DateTime('2024-05-02'));
    $workTime1->setEmployee($employee);

    $workTime2 = new WorkTime();
    $workTime2->setStartTime(new \DateTime('2024-05-03 09:00'));
    $workTime2->setEndTime(new \DateTime('2024-05-03 13:30'));
    $workTime1->setWorkDay(new \DateTime('2024-05-03'));
    $workTime2->setEmployee($employee);

    $this->workTimeRepo
      ->method('findForPeriod')
      ->willReturn([$workTime1, $workTime2]);

    $response = $this->service->summarizeMonth($dto);

    $expectedTotalHours = 8.0 + 4.5; // razem 12.5
    $expectedSalary = 12.5 * 20; // brak nadgodzin, więc 250

    $this->assertIsArray($response);
    $this->assertEquals($expectedTotalHours, $response['response']['ilość normalnych godzin z danego miesiąca']);
    $this->assertEquals($expectedSalary . ' PLN', $response['response']['suma po przeliczeniu']);
  }
}
