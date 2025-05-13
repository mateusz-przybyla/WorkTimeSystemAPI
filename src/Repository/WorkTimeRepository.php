<?php

namespace App\Repository;

use App\Entity\Employee;
use App\Entity\WorkTime;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<WorkTime>
 */
class WorkTimeRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, WorkTime::class);
  }

  /**
   * @return WorkTime[] Returns an array of WorkTime objects
   */
  public function findForPeriod(Employee $employee, \DateTime $start, \DateTime $end): array
  {
    return $this->createQueryBuilder('w')
      ->where('w.employee = :employee')
      ->andWhere('w.workDay BETWEEN :start AND :end')
      ->setParameter('employee', $employee)
      ->setParameter('start', $start)
      ->setParameter('end', $end)
      ->getQuery()
      ->getResult();
  }

  //    /**
  //     * @return WorkTime[] Returns an array of WorkTime objects
  //     */
  //    public function findByExampleField($value): array
  //    {
  //        return $this->createQueryBuilder('w')
  //            ->andWhere('w.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->orderBy('w.id', 'ASC')
  //            ->setMaxResults(10)
  //            ->getQuery()
  //            ->getResult()
  //        ;
  //    }

  //    public function findOneBySomeField($value): ?WorkTime
  //    {
  //        return $this->createQueryBuilder('w')
  //            ->andWhere('w.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->getQuery()
  //            ->getOneOrNullResult()
  //        ;
  //    }
}
