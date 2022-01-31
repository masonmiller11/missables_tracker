<?php

	namespace App\EventListener;
	use App\Repository\UserRepository;
	use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
	use Symfony\Component\HttpFoundation\RequestStack;

	class JWTCreatedListener {

		// src/App/EventListener/JWTCreatedListener.php
		/**
		 * @var RequestStack
		 */
		private RequestStack $requestStack;

		private UserRepository $repository;

		/**
		 * @param RequestStack $requestStack
		 * @param UserRepository $repository
		 */
		public function __construct(RequestStack $requestStack, UserRepository $repository)
		{
			$this->requestStack = $requestStack;
			$this->repository = $repository;
		}

		/**
		 * @param JWTCreatedEvent $event
		 *
		 * @return void
		 */
		public function onJWTCreated(JWTCreatedEvent $event)
		{
			$request = $this->requestStack->getCurrentRequest();

			$payload = $event->getData();
			$user = $this->repository->findOneBy(array('email' => $payload['username']));

			$payload['userHandle'] = $user->getHandle();

			$event->setData($payload);

			$header        = $event->getHeader();
			$header['cty'] = 'JWT';

			$event->setHeader($header);
		}

	}