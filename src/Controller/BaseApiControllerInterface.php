<?php
	namespace App\Controller;

	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Serializer\SerializerInterface;

	interface BaseApiControllerInterface {

		public function create(Request $request): Response;

		public function update(Request $request, int $id): Response;

		public function delete(int $id): Response;

		public function read(int $id, SerializerInterface $serializer): Response;

	}