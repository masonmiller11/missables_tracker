<?php
	namespace App\EventListener;

	use App\Exception\ValidationException;
	use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
	use Symfony\Component\HttpClient\Exception\TransportException;
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

			if ($exception instanceof NotFoundHttpException) {
				$response = new JsonResponse(['status' => 'error',
					'message' => $exception->getMessage() == '' ? 'resource not found' : $exception->getMessage()], Response::HTTP_NOT_FOUND);
			}

			if ($exception instanceof TransportException) {
				$response = new JsonResponse(['status' => 'error',
					'message' => 'can\'t connect with the Internet Game Database'], Response::HTTP_SERVICE_UNAVAILABLE);
			}

			$event->setResponse($response);

		}

	}