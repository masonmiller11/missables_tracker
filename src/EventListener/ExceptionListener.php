<?php
	namespace App\EventListener;

	use App\Exception\ValidationException;
	use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\HttpKernel\Event\ExceptionEvent;
	use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

	class ExceptionListener {

		public function onKernelException(ExceptionEvent $event) {

			$exception = $event->getThrowable();

			$response =  new JsonResponse(['status' => 'error','code' => $exception->getCode(), 'type' => get_class($exception),
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
				$response =  new JsonResponse(['status' => 'error','code' => $exception->getCode(), 'type' => get_class($exception),
					'message' => $exception->getMessage(), 'file' => $exception->getFile()], $exception->getStatusCode());
			}

			if ($exception instanceof NotFoundHttpException) {
				$response = new JsonResponse(['status' => 'error',
					'message' => 'resource not found'], Response::HTTP_NOT_FOUND);
			}

			$event->setResponse($response);

		}

	}