<?php
	namespace App\DTO\Transformer\ResponseTransformer;

	use App\DTO\Game\IGDBGameResponseDTO;

	final class IGDBGameResponseDTOTransformer extends AbstractResponseDTOTransformer {

		/**
		 * @param $object
		 *
		 * @return IGDBGameResponseDTO
		 * @throws \Exception
		 */
		public function transformFromObject($object): IGDBGameResponseDTO {

			$object = $object->toArray()[0];

			$dto = new IGDBGameResponseDTO();

			$dto->releaseDate = new \DateTimeImmutable(date('Y/m/d H:i:s', $object["first_release_date"]));
			$dto->title = $object["name"];
			$dto->artworks = $object["artworks"] ?? [];
			$dto->cover = $object["cover"] ?? 0;
			$dto->rating = $object["rating"] ?? floatval(0); //TODO we should fix this. We need the database to be okay if this is empty.
			$dto->summary = $object["summary"] ?? 'No summary available';
			$dto->storyline = $object["storyline"] ?? 'No story available';
			$dto->slug = $object["slug"] ?? [];
			$dto->screenshots = $object["screenshots"] ?? [];
			$dto->platforms = $object["platforms"] ?? [];
			$dto->internetGameDatabaseID = $object["id"];
			$dto->genre = $object["genre"] ?? 'No genre available'; //TODO let's fix these at some point

			return $dto;

		}

	}