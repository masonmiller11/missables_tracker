<?php
	namespace App\DTO\Transformer\RequestTransformer;

	use App\DTO\GameDTO;
	use Symfony\Component\HttpFoundation\Request;

	class GameRequestDTOTransformer extends AbstractRequestDTOTransformer {

		public function transformFromRequest(Request $request): GameDTO {

			$data = json_decode($request->getContent(), true);

			$dto = new GameDTO();

			$dto->title = $data['title'] ?? '';
			$dto->genre = $data['genre'] ?? 'genre not available';
			$dto->releaseDate = $data['release_date'] ?? '';
			$dto->rating = $data['rating'] ?? 'rating not available';
			$dto->summary = $data['summary'] ?? 'summary not available';
			$dto->storyline = $data['storyline'] ?? 'storyline not available';
			$dto->slug = $data['slug'] ?? '';
			$dto->screenshots = $data['screenshots'] ?? [];
			$dto->platforms = $data['platforms'] ?? [];
			$dto->cover = $data['cover'] ?? 'cover not available';
			$dto->artworks = $data['artworks'] ?? [];
			$dto->playthroughTemplates = $data['templates'] ?? [];
			$dto->internetGameDatabaseID = $data['igdb_id'] ?? '';

			return $dto;

		}

	}