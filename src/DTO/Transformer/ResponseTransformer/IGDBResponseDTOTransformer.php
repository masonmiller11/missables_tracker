<?php
	namespace App\DTO\Transformer\ResponseTransformer;

	use App\DTO\IGDBResponseDTO;

	class IGDBResponseDTOTransformer extends AbstractResponseDTOTransformer {

		/**
		 * @param $object
		 *
		 * @return IGDBResponseDTO
		 */
		public function transformFromObject($object): IGDBResponseDTO {

			$object = $object->toArray()[0];

			$dto = new IGDBResponseDTO();

			$dto->releaseDate = new \DateTimeImmutable(date('Y/m/d H:i:s', $object["first_release_date"]));
			$dto->title = $object["name"];
			$dto->artworks = $object["artworks"];
			$dto->cover = $object["cover"];
			$dto->rating = $object["rating"];
			$dto->summary = $object["summary"];
			$dto->storyline = $object["storyline"] ?? 'No story available';
			$dto->slug = $object["slug"];
			$dto->screenshots = $object["screenshots"];
			$dto->platforms = $object["platforms"];
			$dto->id = $object["id"];
			$dto->genre = $object["genre"] ?? 'No genre available'; //TODO let's fix these at some point

			return $dto;

		}

	}