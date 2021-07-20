<?php
	namespace App\EventListener;

	use App\Exception\ValidationException;
	use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\HttpKernel\Event\ExceptionEvent;
	use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

	class ExceptionListener {

		public function onKernelException(ExceptionEvent $event) {

			$exception = $event->getThrowable();

			$response =  new JsonResponse(['status' => 'error','code' => $exception->getCode(),
				'message' => $exception->getMessage(), 'file' => $exception->getFile()], Response::HTTP_INTERNAL_SERVER_ERROR);

			if ($exception instanceof UniqueConstraintViolationException) {
				$response =  new JsonResponse(['status' => 'error',
					'message' => 'duplicate resource'], Response::HTTP_CONFLICT);
			}

			if ($exception instanceof ValidationException) {
				$response = new JsonResponse(['status' => 'error',
					'message' => 'validation failed'], Response::HTTP_BAD_REQUEST);
			}

			if ($exception instanceof HttpExceptionInterface) {
				$response =  new JsonResponse(['status' => 'error',
					'message' => $exception->getMessage(),'type' =>gettype($exception), 'file' => $exception->getFile()], $exception->getStatusCode());
			}

			$event->setResponse($response);

		}

	}