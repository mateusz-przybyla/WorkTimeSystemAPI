<?php

namespace App\Controller;

use App\Dto\WorkTimeDto;
use App\Service\WorkTimeService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class WorkTimeController extends AbstractController
{
  #[Route('/api/work-time', name: 'api_create_work_time', methods: ['POST'])]
  public function create(
    Request $request,
    ValidatorInterface $validator,
    SerializerInterface $serializer,
    WorkTimeService $workTimeService,
  ): JsonResponse {
    try {
      $dto = $serializer->deserialize($request->getContent(), WorkTimeDto::class, 'json',  [
        'datetime_format' => 'd.m.Y H:i',
      ]);
    } catch (\Exception $e) {
      return $this->json(['error' => 'Nieprawidłowy format JSON.'], Response::HTTP_BAD_REQUEST);
    }

    $errors = $validator->validate($dto);
    if (count($errors) > 0) {
      $errorMessages = [];
      foreach ($errors as $error) {
        $errorMessages[$error->getPropertyPath()][] = $error->getMessage();
      }
      return $this->json(['error' => $errorMessages], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    $businessErrors = $dto->validateWorkTimeBusinessLogic();
    if (count($businessErrors) > 0) {
      return $this->json(['error' => $businessErrors], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    $result = $workTimeService->register($dto);

    return $this->json($result, Response::HTTP_CREATED);
  }
}
