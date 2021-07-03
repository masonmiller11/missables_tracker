<?php

	namespace App\DTO\Transformer\RequestTransformer;

	use App\DTO\Request\GameRequestDTO;
	use Symfony\Component\HttpFoundation\Request;

	class GameRequestDTOTransformer {

		public function transformFromRequest(Request $request): GameRequestDTO {

			$data = json_decode($request->getContent(), true);

			$dto = new GameRequestDTO();

			$dto->title = $data['title'];
			$dto->developer = $data['developer'];
			$dto->genre = $data['genre'];
			$dto->releaseDate = \DateTimeImmutable::createFromFormat('Y-m-d',$data['release_date']);

			return $dto;

		}

	}