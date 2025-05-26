<?php

namespace App\Controller;

use App\Dto\EmployeeDto;
use App\Service\EmployeeService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class EmployeeController extends AbstractController
{
  #[Route('/api/employee', name: 'api_create_employee', methods: ['POST'])]
  public function create(
    Request $request,
    ValidatorInterface $validator,
    SerializerInterface $serializer,
    EmployeeService $employeeService,
    TranslatorInterface $translator
  ): JsonResponse {
    try {
      $dto = $serializer->deserialize($request->getContent(), EmployeeDto::class, 'json');
    } catch (\Exception $e) {
      return $this->json(['error' =>  $translator->trans('invalid_json_format', [], 'exceptions')], Response::HTTP_BAD_REQUEST);
    }

    $errors = $validator->validate($dto);
    if (count($errors) > 0) {
      $errorMessages = [];
      foreach ($errors as $error) {
        $errorMessages[$error->getPropertyPath()][] = $error->getMessage();
      }
      return $this->json(['error' => $errorMessages], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    $employee = $employeeService->create($dto);

    return $this->json(['response' => ['uuid' => $employee->getUuid()]], Response::HTTP_CREATED);
  }
}
