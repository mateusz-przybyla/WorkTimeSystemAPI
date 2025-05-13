<?php

namespace App\Controller;

use App\Entity\Employee;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class EmployeeController extends AbstractController
{
  #[Route('/api/employee', name: 'api_create_employee', methods: ['POST'])]
  public function index(
    Request $request,
    EntityManagerInterface $entityManager
  ): JsonResponse {
    $data = json_decode($request->getContent(), true);

    $employee = new Employee();
    $employee->setFirstname($data['imię']);
    $employee->setSurname($data['nazwisko']);

    $entityManager->persist($employee);
    $entityManager->flush();

    return new JsonResponse([
      'response' => ['id' => $employee->getId()]
    ], Response::HTTP_CREATED);
  }
}
