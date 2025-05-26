<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ApiExceptionListener
{
  public function __construct(private TranslatorInterface $translator) {}

  public function onKernelException(ExceptionEvent $event): void
  {
    $exception = $event->getThrowable();

    $statusCode = $exception instanceof HttpExceptionInterface
      ? $exception->getStatusCode()
      : 500;

    $translatedMessage = $this->translator->trans($exception->getMessage(), [], 'exceptions');
    $response = new JsonResponse([
      'error' => $translatedMessage,
    ], $statusCode);

    $event->setResponse($response);
  }
}
