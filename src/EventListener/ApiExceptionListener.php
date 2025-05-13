<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ApiExceptionListener
{
  public function onKernelException(ExceptionEvent $event): void
  {
    $exception = $event->getThrowable();

    $statusCode = $exception instanceof HttpExceptionInterface
      ? $exception->getStatusCode()
      : 500;

    $response = new JsonResponse([
      'error' => $exception->getMessage(),
    ], $statusCode);

    $event->setResponse($response);
  }
}
