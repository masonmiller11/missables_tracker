<?php
	namespace App\DTO\Transformer\ResponseTransformer;

	use App\DTO\IGDBResponseDTO;
	use Lcobucci\JWT\Exception;

	class IGDBResponseDTOTransformer extends AbstractResponseDTOTransformer {

		/**
		 * @param $object
		 *
		 * @return IGDBResponseDTO
		 * @throws \Exception
		 */
		public function transformFromObject($object): IGDBResponseDTO {

			$object = $object->toArray()[0];

			$dto = new IGDBResponseDTO();

			try {

				$dto->releaseDate = new \DateTimeImmutable(date('Y/m/d H:i:s', $object["first_release_date"]));
				$dto->title = $object["name"];
				$dto->artworks = $object["artworks"] ?? [];
				$dto->cover = $object["cover"] ?? [];
				$dto->rating = $object["rating"] ?? floatval(0); //TODO we should fix this. We need the database to be okay if this is empty.
				$dto->summary = $object["summary"] ?? [];
				$dto->storyline = $object["storyline"] ?? 'No story available';
				$dto->slug = $object["slug"] ?? [];
				$dto->screenshots = $object["screenshots"] ?? [];
				$dto->platforms = $object["platforms"] ?? [];
				$dto->id = $object["id"];
				$dto->genre = $object["genre"] ?? 'No genre available'; //TODO let's fix these at some point

			} catch (\ErrorException $e) {

				throw new \RuntimeException('There was an issue with the data we got from the Internet Game 
				Database.');

			}

			return $dto;

		}

	}