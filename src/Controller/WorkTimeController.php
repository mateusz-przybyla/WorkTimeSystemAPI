<?php

namespace App\Controller;

use App\Dto\WorkTimeDto;
use App\Dto\WorkTimeSummaryDto;
use App\Service\WorkTimeService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\Translation\TranslatorInterface;

final class WorkTimeController extends AbstractController
{
  #[Route('/api/work-time', name: 'api_create_work_time', methods: ['POST'])]
  public function create(
    Request $request,
    ValidatorInterface $validator,
    SerializerInterface $serializer,
    WorkTimeService $workTimeService,
    TranslatorInterface $translator
  ): JsonResponse {
    try {
      $dto = $serializer->deserialize($request->getContent(), WorkTimeDto::class, 'json',  [
        'datetime_format' => 'd.m.Y H:i',
      ]);
    } catch (\Exception $e) {
      return $this->json(['error' => $translator->trans('invalid_json_format', [], 'exceptions')], Response::HTTP_BAD_REQUEST);
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
      $translatedErrors = array_map(fn($key) => $translator->trans($key), $businessErrors);
      return $this->json(['error' => $translatedErrors], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    $workTimeService->register($dto);

    return $this->json(['response' => [$translator->trans('success.worktime_added_successfully')]], Response::HTTP_CREATED);
  }

  #[Route('/api/summary/day', name: 'api_summary_day', methods: ['POST'])]
  public function showDaySummary(
    Request $request,
    ValidatorInterface $validator,
    SerializerInterface $serializer,
    WorkTimeService $workTimeService,
    TranslatorInterface $translator
  ): JsonResponse {
    try {
      $dto = $serializer->deserialize($request->getContent(), WorkTimeSummaryDto::class, 'json',  [
        'datetime_format' => 'd.m.Y',
      ]);
    } catch (\Exception $e) {
      return $this->json(['error' => $translator->trans('invalid_json_format', [], 'exceptions')], Response::HTTP_BAD_REQUEST);
    }

    $errors = $validator->validate($dto);
    if (count($errors) > 0) {
      $errorMessages = [];
      foreach ($errors as $error) {
        $errorMessages[$error->getPropertyPath()][] = $error->getMessage();
      }
      return $this->json(['error' => $errorMessages], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    $result = $workTimeService->summarizeDay($dto);

    return $this->json($result, Response::HTTP_OK);
  }

  #[Route('/api/summary/month', name: 'api_summary_month', methods: ['POST'])]
  public function showMonthSummary(
    Request $request,
    ValidatorInterface $validator,
    SerializerInterface $serializer,
    WorkTimeService $workTimeService,
    TranslatorInterface $translator
  ): JsonResponse {
    try {
      $dto = $serializer->deserialize($request->getContent(), WorkTimeSummaryDto::class, 'json',  [
        'datetime_format' => 'm.Y',
      ]);
    } catch (\Exception $e) {
      return $this->json(['error' => $translator->trans('invalid_json_format', [], 'exceptions')], Response::HTTP_BAD_REQUEST);
    }

    $errors = $validator->validate($dto);
    if (count($errors) > 0) {
      $errorMessages = [];
      foreach ($errors as $error) {
        $errorMessages[$error->getPropertyPath()][] = $error->getMessage();
      }
      return $this->json(['error' => $errorMessages], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    $result = $workTimeService->summarizeMonth($dto);

    return $this->json($result, Response::HTTP_OK);
  }
}
